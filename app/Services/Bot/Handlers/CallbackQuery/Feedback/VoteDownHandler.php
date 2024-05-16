<?php 

namespace App\Services\Bot\Handlers\CallbackQuery\Feedback;

use App\Services\Bot\Answer\DetailedFeedbackMessage;
use App\Services\Bot\Tool\UpdateTree;

class VoteDownHandler extends AbstractFeedbackVoteHandler
{
    public const CALLBACK_DATA = 'feedback_vote_down';

    public function getCallbackData(): string
    {
        return self::CALLBACK_DATA;
    }

    public static function isSuitable(string $callbackData): bool
    {
        return $callbackData === self::CALLBACK_DATA;
    }

    protected function getPollAnswer(): string 
    {
        return 'vote_down';
    }

    public function handle(\TelegramBot\Api\Types\Update $update): void
    {
        parent::handle($update);

        $this->getBot()->getLogger()->info("Asking user " . $this->getBot()->getUser()->username . " more details");

        $this->getBot()->sendTo(UpdateTree::getChat($update)->getId(), new DetailedFeedbackMessage());
    }
}