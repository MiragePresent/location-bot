<?php 

namespace App\Services\Bot\Handlers\Commands;

use App\Services\Bot\Answer\AskFeedbackMessage;
use App\Services\Bot\Handlers\AbstractCommandHandler;
use App\Services\Bot\Bot;
use TelegramBot\Api\Types\Message;

class FeedbackCommand extends AbstractCommandHandler
{
    
    public function getSignature(): string
    {
        return "feedback";
    }

    public static function dispatch(Bot $bot, Message $message)
    {
        $self = new static($bot);
        $self->handle($message);
    }

    public function handle(Message $message): void
    {
        $this->bot->getLogger()->info("Sending basic feedback request message to user: " . $this->bot->getUser()->username);
        $this->bot->sendTo($this->bot->getUser()->chat_id, new AskFeedbackMessage());
    }
}