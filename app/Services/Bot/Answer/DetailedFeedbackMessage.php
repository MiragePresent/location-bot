<?php 

namespace App\Services\Bot\Answer;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use App\Services\Bot\Handlers\CallbackQuery\DetailedFeedback;

class DetailedFeedbackMessage extends TextAnswer
{
    private array $hidenButtons = [];

    public function __construct() {
        parent::__construct(trans("bot.messages.text.give_me_detailed_feedback"));
    }

    public function hideButton(string $button): void 
    {
        $this->hidenButtons[] = $button;
    }

    public function getMarkup()
    {
        $buttons = [];
        $possibleAnswers = [
            DetailedFeedback\WrongAddressHandler::CALLBACK_DATA,
            DetailedFeedback\NeedsBetterUxHandler::CALLBACK_DATA,
            DetailedFeedback\NoCountryHandler::CALLBACK_DATA,
            DetailedFeedback\MoreFunctionsHandler::CALLBACK_DATA,
            DetailedFeedback\ContactCreatorHandler::CALLBACK_DATA,
            DetailedFeedback\NoAnswerHandler::CALLBACK_DATA,
        ];

        foreach ($possibleAnswers as $answer) {
            if (in_array($answer, $this->hidenButtons)) {
                continue;
            }

            $buttons[] = [[
                "text" => trans("bot.interface.button." . $answer),
                "callback_data" => $answer,
            ]];
        }

        return new InlineKeyboardMarkup($buttons);
    }
}
