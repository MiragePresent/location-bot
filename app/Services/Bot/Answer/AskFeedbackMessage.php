<?php 

namespace App\Services\Bot\Answer;

use App\Services\Bot\Handlers\CallbackQuery\Feedback\VoteDownHandler;
use App\Services\Bot\Handlers\CallbackQuery\Feedback\VoteUpHandler;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class AskFeedbackMessage extends TextAnswer 
{
    public function __construct()
    {
        parent::__construct(trans('bot.messages.text.give_me_feedback'));
    }

    public function getMarkup() 
    {
        $voteUp = [
            "text" => trans("bot.interface.button.vote_up"),
            "callback_data" => VoteUpHandler::CALLBACK_DATA,
        ];
        $voteDown = [
            "text" => trans("bot.interface.button.vote_down"),
            "callback_data" => VoteDownHandler::CALLBACK_DATA,
        ];

        return new InlineKeyboardMarkup([
            [$voteUp],
            [$voteDown],
        ]);
    } 
}