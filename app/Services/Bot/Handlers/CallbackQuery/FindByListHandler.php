<?php

namespace App\Services\Bot\Handlers\CallbackQuery;

use App\Services\Bot\Handlers\AbstractUpdateHandler;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use TelegramBot\Api\Types\Update;

/**
 * Class FindByListHandler
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  10.06.2019
 */
class FindByListHandler extends AbstractUpdateHandler implements CallbackQueryHandlerInterface
{
    /**
     * Callback identity name
     *
     * @var string
     */
    public const CALLBACK_DATA = "search_by_list";

    /**
     * @inheritDoc
     */
    public function getCallbackData(): string
    {
        return static::CALLBACK_DATA;
    }

    /**
     * @inheritDoc
     */
    public function handle(Update $update): void
    {
        $this->bot->log(sprintf(
           "CallbackQuery: %s \nFrom: %s",
           $update->getCallbackQuery()->getData(),
           $update->getCallbackQuery()->getFrom()->toJson()
        ));

        $kb = new ReplyKeyboardMarkup(
            [[
                ["text" => "Вінницька обл."],
                ["text" => "Волинська обл."],
                ["text" => "Дніпропетровська обл."],
                ["text" => "Донецька обл."],
                ["text" => "Житомирська обл."],
            ]],
            true,
            true
        );

        $this->bot->reply($update->getCallbackQuery()->getMessage(), "В якій області ти шукаєш церкву?", $kb);
    }
}
