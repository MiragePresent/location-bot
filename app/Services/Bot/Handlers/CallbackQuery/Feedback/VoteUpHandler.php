<?php 

namespace App\Services\Bot\Handlers\CallbackQuery\Feedback;

class VoteUpHandler extends AbstractFeedbackVoteHandler
{
    public const CALLBACK_DATA = 'feedback_vote_up';

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
        return 'vote_up';
    }
}