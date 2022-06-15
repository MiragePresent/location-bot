<?php

namespace App\Services\Bot\Answer;

use App\Services\Bot\Handlers\CallbackQuery\FindByList;
use App\Services\Bot\Handlers\CallbackQuery\FindByLocation;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

/**
 * Class FindAnswer
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  15.06.2022
 */
class FindChurchAnswer implements AnswerInterface
{

    public function __construct(protected string $text) {}

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Searching church methods
     *
     * @return InlineKeyboardMarkup
     */
    public function getMarkup()
    {
        $byList = [
            "text" => trans("bot.interface.button.find_by_list"),
            "callback_data" => FindByList::CALLBACK_DATA,
        ];
        $byLocation = [
            "text" => trans("bot.interface.button.find_by_location"),
            "callback_data" => FindByLocation::CALLBACK_DATA,
        ];

        return new InlineKeyboardMarkup([[$byList], [$byLocation]]);
    }
}
