<?php 

namespace App\Services\Bot\Handlers\CallbackQuery\DetailedFeedback;

use App\Services\Bot\Answer\HelpProjectAnswer;

class WrongAddressHandler extends AbstractDetailedFeedbackVoteHandler 
{
    public const CALLBACK_DATA = "vote_detailed_incorrect_addresses";

    protected function sendNextMessage(\TelegramBot\Api\Types\Chat $chat): void
    {
        $support = $this->getBot()->getSupportInfo();
        $answer = new HelpProjectAnswer($support['channel']['name'], $support['channel']['link']);

        $this->getBot()->sendTo($chat->getId(), $answer);

        parent::sendNextMessage($chat);
    }
}
