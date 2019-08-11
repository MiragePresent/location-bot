<?php

namespace App\Services\Bot\Message;

use App\Services\Bot\Handlers\CallbackQuery\FindByList;
use App\Services\Bot\Handlers\CallbackQuery\FindByLocation;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

/**
 * Class FindMessage
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  11.08.2019
 */
class FindMessage implements MessageInterface
{
    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return trans("bot.messages.text.find");
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

        return new InlineKeyboardMarkup([[ $byList, $byLocation ]]);
    }
}
