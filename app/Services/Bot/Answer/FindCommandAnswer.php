<?php

namespace App\Services\Bot\Answer;

use App\Services\Bot\Handlers\CallbackQuery\FindByList;
use App\Services\Bot\Handlers\CallbackQuery\FindByLocation;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

/**
 * Class FindMessage
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  11.08.2019
 */
class FindCommandAnswer extends FindChurchAnswer
{
    public function __construct()
    {
        parent::__construct(trans("bot.messages.text.find"));
    }
}
