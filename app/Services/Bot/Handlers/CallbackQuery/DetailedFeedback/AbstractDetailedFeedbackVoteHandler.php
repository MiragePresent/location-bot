<?php 

namespace App\Services\Bot\Handlers\CallbackQuery\DetailedFeedback;

use App\Models\PollAnswer;
use App\Services\Bot\Answer\DetailedFeedbackMessage;
use App\Services\Bot\Handlers\AbstractUpdateHandler;
use App\Services\Bot\Tool\UpdateTree;
use App\Services\Bot\Tracker\StatsTrackerInterface;
use App\Services\Bot\UserPoll;
use TelegramBot\Api\Types\Chat;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\Update;
use App\Services\Bot\Handlers\CallbackQuery\CallbackQueryHandlerInterface;

abstract class AbstractDetailedFeedbackVoteHandler extends AbstractUpdateHandler implements CallbackQueryHandlerInterface 
{
    public const CALLBACK_DATA = "";

    public static function isSuitable(string $callbackData): bool
    {
        return static::CALLBACK_DATA === $callbackData;
    }

    public function getCallbackData(): string
    {
        return static::CALLBACK_DATA;
    }

    protected function getPollAnswer(): string
    {
        return str_replace("vote_detailed_", "", static::CALLBACK_DATA);
    }

    protected function possibleAnswers(): array 
    {
        return [
            WrongAddressHandler::CALLBACK_DATA,
            NeedsBetterUxHandler::CALLBACK_DATA,
            NoCountryHandler::CALLBACK_DATA,
            MoreFunctionsHandler::CALLBACK_DATA,
            ContactCreatorHandler::CALLBACK_DATA,
            NoAnswerHandler::CALLBACK_DATA,
        ];
    }

    protected function sendNextMessage(Chat $chat): void 
    {
        $this->getBot()->getLogger()->info("Askin another feedback from " . $this->getBot()->getUser()->username);
        $msg = new DetailedFeedbackMessage();
        $msg->hideButton($this->getCallbackData());

        $this->getBot()->sendTo($chat->getId(), $msg);
    }

    public function handle(Update $update): void
    {
        $this->bot->getStatsTracker()->setRequestType(StatsTrackerInterface::REQUEST_TYPE_INTERFACE_INTERACTION);

        $pollAnswer = new PollAnswer();
        $pollAnswer->user_id = $this->getBot()->getUser()->id;
        $pollAnswer->poll_name = UserPoll::DetailedFeedback->value;
        $pollAnswer->answer = $this->getPollAnswer();
        $pollAnswer->save();

        // Delete buttons
        $message = UpdateTree::getMessage($update);
        $this->getBot()->getApi()->editMessageReplyMarkup(
            $message->getChat()->getId(),
            $message->getMessageId(),
            replyMarkup: new InlineKeyboardMarkup(),
        );
        $updatedMessageText = trans(
            "bot.messages.text.give_me_detailed_feedback_answered", 
            ["answer" => trans("bot.interface.button." . $this->getCallbackData())]
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

        $this->sendNextMessage(UpdateTree::getChat($update));
    }
}