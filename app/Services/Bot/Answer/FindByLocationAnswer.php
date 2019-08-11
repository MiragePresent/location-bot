<?php

namespace App\Services\Bot\Answer;

use TelegramBot\Api\Types\ReplyKeyboardMarkup;

/**
 * Class FindByLocationAnswer
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  11.08.2019
 */
class FindByLocationAnswer implements AnswerInterface
{
    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return trans("bot.messages.text.find_by_location");
    }

    /**
     * @inheritDoc
     */
    public function getMarkup()
    {
        return new ReplyKeyboardMarkup([[
            ["text" => trans("bot.interface.button.my_location"), "request_location" => true]
        ]], true, true);
    }
}
