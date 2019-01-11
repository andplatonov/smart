<?php
use App\Http\Controllers\BotManController;
use BotMan\BotMan\BotMan;
use App\Conversations\BenchConversation;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Attachments\Image;
use App\Http\Middleware\TypingMiddleware;


$botman = resolve('botman');

if($botman->driverStorage()->getDefaultKey() == 'Telegram'){
    $typingMiddleware = new TypingMiddleware();
    $botman->middleware->sending($typingMiddleware);
}

$botman->hears('Hi', function ($bot) {
    $bot->reply('Hello!');
});
$botman->hears('Start conversation', BotManController::class.'@startConversation');

$botman->hears('/start|начать|старт|приступить', function (BotMan $bot) {
    $bot->startConversation(new BenchConversation());
});

