<?php

use App\Question;
use App\Answer;
use App\PreQuestion;
use App\PostQuestion;
use Illuminate\Database\Seeder;

class QuestionAnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Question::truncate();
        Answer::truncate();
        PreQuestion::truncate();
        PostQuestion::truncate();
        $questions = $this->getData();

        $questions->each(function ($question) {
            $createdQuestion = Question::create([
                'text' => $question['question'],
            ]);

            collect($question['answers'])->each(function ($answer) use ($createdQuestion) {
                Answer::create([
                    'question_id' => $createdQuestion->id,
                    'text' => $answer['text'],
                ]);
            });

            collect($question['pre_questions'])->each(function ($pre_question) use ($createdQuestion) {
                PreQuestion::create([
                    'question_id' => $createdQuestion->id,
                    'text' => $pre_question['text'],
                ]);
            });

            collect($question['post_questions'])->each(function ($post_question) use ($createdQuestion) {
                PostQuestion::create([
                    'question_id' => $createdQuestion->id,
                    'text' => $post_question['text'],
                ]);
            });

        });
    }

    private function getData()
    {
        return collect([
            [
                'pre_questions' => [
                    ['text' => 'Для начала посмотри вводное видео'],
                    ['text' => 'https://youtu.be/XQrb_3doTlI'],
                ],
                'question' => 'Посмотрел?',
                'answers' => [
                    ['text' => 'Да'],
                    ['text' => 'Нет'],
                ],
                'post_questions' => [],
            ],[
                'pre_questions' => [
                    ['text' => 'Проверим, как ты усвоил материал.'],
                ],
                'question' => 'Из списка выше выбери правильные критерии:',
                'answers' => [
                    ['text' => 'Б, В, Е, Ё, И'],
                    ['text' => 'А, Д, Ж, З, И'],
                    ['text' => 'А, Г, Е, Ж, З'],
                    ['text' => 'Г, Д, Е, Ж, И'],
                ],
                'post_questions' => [
                    ['text' => 'Цель должна быть:
                    
                    А. Измерима
                    Б. Визуально прорисована
                    В. Описана красивым языком
                    Г. Конкретна
                    Д. Осознанна
                    Е. Амбициозна
                    Ё. Вызывать улыбку
                    Ж. Ресурсна
                    З. Определена по времени
                    И. Твоя собственная'],
                ],
            ],[
                'pre_questions' => [
                    ['text' => 'А теперь давай попробуем применить знания на практике.'],
                ],
                'question' => 'Напиши свою цель по SMART, а мой приятель эксперт проверит ее и мы пришлём тебе обратную связь:',
                'answers' => [],
                'post_questions' => [],
            ]
        ]);
    }
}
