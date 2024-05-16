<?php 

namespace App\Services\Bot\Handlers\CallbackQuery\Feedback;

use App\Models\PollAnswer;
use App\Services\Bot\Handlers\AbstractUpdateHandler;
use App\Services\Bot\Tool\UpdateTree;
use App\Services\Bot\Tracker\StatsTrackerInterface;
use App\Services\Bot\UserPoll;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\Update;
use App\Services\Bot\Handlers\CallbackQuery\CallbackQueryHandlerInterface;

abstract class AbstractFeedbackVoteHandler extends AbstractUpdateHandler implements CallbackQueryHandlerInterface 
{
    abstract protected function getPollAnswer(): string;

    public function handle(Update $update): void
    {
        $this->bot->getStatsTracker()->setRequestType(StatsTrackerInterface::REQUEST_TYPE_INTERFACE_INTERACTION);

        $pollAnswer = new PollAnswer();
        $pollAnswer->user_id = $this->getBot()->getUser()->id;
        $pollAnswer->poll_name = UserPoll::BasicFeedback->value;
        $pollAnswer->answer = $this->getPollAnswer();
        $pollAnswer->save();

        // Delete buttons
        $message = UpdateTree::getMessage($update);
        $this->getBot()->getApi()->editMessageReplyMarkup(
            $message->getChat()->getId(),
            $message->getMessageId(),
            replyMarkup: new InlineKeyboardMarkup()
        );
        $updatedMessageText = trans(
            "bot.messages.text.give_me_feedback_answered", 
            ["answer" => trans("bot.interface.button." . $this->getPollAnswer())]
        );
        $this->getBot()->getApi()->editMessageText(
            UpdateTree::getChat($update)->getId(),
            $message->getMessageId(),
            $updatedMessageText
        );

        // Say thank you
        $this->bot->getApi()->answerCallbackQuery(
            $update->getCallbackQuery()->getId(),
            trans("bot.interface.notification.thank_you")
        );
    }
}