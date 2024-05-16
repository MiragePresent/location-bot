<?php 

namespace App\Services\Bot\Handlers\CallbackQuery\DetailedFeedback;

class NoAnswerHandler extends AbstractDetailedFeedbackVoteHandler 
{
    public const CALLBACK_DATA = "vote_detailed_no_answer";

    public function sendNextMessage(\TelegramBot\Api\Types\Chat $chat): void {}
}
