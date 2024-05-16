<?php 

namespace App\Services\Bot\Handlers\Commands;

use App\Models\PollAnswer;
use App\Services\Bot\Answer\AskFeedbackMessage;
use App\Services\Bot\Handlers\AbstractCommandHandler;
use App\Services\Bot\Bot;
use App\Services\Bot\UserPoll;
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

        $userPoll = new PollAnswer();
        $userPoll->user_id = $this->bot->getUser()->id;
        $userPoll->poll_name = UserPoll::BasicFeedback->value;
        $userPoll->answer = "message_sent";
        $userPoll->save();
    }
}