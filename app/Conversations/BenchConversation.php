<?php

namespace App\Conversations;

use App\Question;
use App\Answer;
use App\PreQuestion;
use App\Interviewer;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer as BotManAnswer;
use BotMan\BotMan\Messages\Outgoing\Question as BotManQuestion;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class BenchConversation extends Conversation
{

    protected $quizQuestions;
    protected $userPoints = 0;
    protected $userCorrectAnswers = 0;
    protected $questionCount = 0;
    protected $currentQuestion = 1;
    protected $data;

    public function run()
    {
        $this->data = [];
        $this->quizQuestions = Question::all();
        $this->questionCount = $this->quizQuestions->count();
        $this->quizQuestions = $this->quizQuestions->keyBy('id');
        $this->showInfo();
    }

    private function showInfo()
    {
        $this->say('Привет, я SMART-бот!');
        $this->say('Я научу тебя ставить цели профессионально.');

        $this->readyToGo();
    }

    private function readyToGo(){

        $questionTemplate = BotManQuestion::create('Готов начать?');
        $questionTemplate->addButton(Button::create('Да')->value('Да'));
        $questionTemplate->addButton(Button::create('Нет')->value('Нет'));

        $this->ask($questionTemplate, function (BotManAnswer $answer){
            if($answer->getText() == 'Да'){
                $this->say('Отлично! Приступим.');
                $this->checkForNextQuestion();
            }else{
                $this->say('Когда будешь готов, нажмите «Да»');
                return $this->readyToGo();
            }
        });

    }

    private function checkForNextQuestion()
    {
        if ($this->quizQuestions->count()) {
            return $this->askQuestion($this->quizQuestions->first());
        }

        $this->showResult();

    }

    private function askQuestion(Question $question)
    {

        if($question->pre_questions){
            foreach ($question->pre_questions as $pre_question){
                $this->say($pre_question->text);
            }
        }

        if($question->post_questions){
            foreach ($question->post_questions as $post_question){
                $this->say($post_question->text);
            }
        }

        $questionTemplate = BotManQuestion::create($question->text);

        foreach ($question->answers as $answer) {
            $questionTemplate->addButton(Button::create($answer->text)->value($answer->id));
        }


        $this->ask($questionTemplate, function (BotManAnswer $answer) use ($question) {

            array_push($this->data, $answer->getText());

            if($question->id == '1'){
                if($answer->getText()=='2'){
                    $this->say('Ты обязательно должен его посмотреть, иначе не поймешь сути. Попробуем снова');
                    return $this->checkForNextQuestion();
                }else{
                    $this->say('Отлично!');
                }
            }

            if($question->id == '2'){
                if($answer->getText()=='5'){
                    $this->say('Молодец!');
                }else{
                    $this->say('Неправильный ответ, попробуй еще раз');
                    return $this->checkForNextQuestion();
                }
            }


            $this->quizQuestions->forget($question->id);

            $this->checkForNextQuestion();
        });
    }

    private function showResult()
    {

        $interviewer = new Interviewer();

        $interviewer->answers = json_encode($this->data);
        $interviewer->save();

        $this->say('Спасибо! Жди обратной связи.');
    }
}
