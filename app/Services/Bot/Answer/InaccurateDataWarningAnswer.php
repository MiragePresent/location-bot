<?php

namespace App\Services\Bot\Answer;

use App\Services\Bot\Handlers\CallbackQuery\HelpProjectAlert;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

/**
 * Class InaccurateDataWarningAnswer
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  20.06.2021
 */
class InaccurateDataWarningAnswer implements AnswerInterface
{
    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return trans('bot.messages.text.inaccurate_data');
    }

    /**
     * @inheritDoc
     */
    public function getMarkup()
    {
        $help = [
            'text' => trans('bot.interface.button.help_project'),
            'callback_data' => HelpProjectAlert::CALLBACK_DATA,
        ];

        return new InlineKeyboardMarkup([[$help]]);
    }
}
