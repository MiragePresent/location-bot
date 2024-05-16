<?php 

namespace App\Services\Bot\Handlers\CallbackQuery\DetailedFeedback;

class WrongAddressHandler extends AbstractDetailedFeedbackVoteHandler 
{
    public const CALLBACK_DATA = "vote_detailed_incorrect_addresses";
}
