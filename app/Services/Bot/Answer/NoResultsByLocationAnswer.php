<?php

namespace App\Services\Bot\Answer;

use App\Services\Bot\Handlers\CallbackQuery\FindByList;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

/**
 * Class NoResultsByLocationAnswer
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  23.03.2022
 */
class NoResultsByLocationAnswer implements AnswerInterface
{
    /**
     * @var int
     */
    private $searchRadius;

    public function __construct(int $searchRadiusKm)
    {
        $this->searchRadius = $searchRadiusKm;
    }

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return trans('bot.messages.text.no_results_found', ['radius' => $this->searchRadius]);
    }

    /**
     * @inheritDoc
     */
    public function getMarkup()
    {
        $byList = [
            "text" => trans("bot.interface.button.find_by_list"),
            "callback_data" => FindByList::CALLBACK_DATA,
        ];

        return new InlineKeyboardMarkup([[$byList]]);
    }
}
