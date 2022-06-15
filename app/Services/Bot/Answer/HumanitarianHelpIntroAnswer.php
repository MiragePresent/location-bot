<?php

namespace App\Services\Bot\Answer;

use App\Services\Bot\Handlers\CallbackQuery\GetHumanitarianHelp;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

/**
 * Class HumanitarianHelpAnswer
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  15.06.2022
 */
class HumanitarianHelpIntroAnswer implements AnswerInterface
{

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return trans("bot.messages.text.humanitarian_help_request");
    }

    /**
     * @inheritDoc
     */
    public function getMarkup()
    {
        return new InlineKeyboardMarkup([[
            [
                "text" => trans("bot.interface.button.get_help"),
                "callback_data" => GetHumanitarianHelp::CALLBACK_DATA,
            ]
        ]]);
    }
}
