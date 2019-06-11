<?php

namespace App\Services\Bot\Handlers\CallbackQuery;

use App\Services\Bot\Handlers\AbstractUpdateHandler;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use TelegramBot\Api\Types\Update;

/**
 * Class SearchByListHandler
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  10.06.2019
 */
class SearchByListUpdateHandler
    extends AbstractUpdateHandler
    implements CallbackQueryHandlerInterface
{
    /**
     * Callback identity name
     *
     * @var string
     */
    public const CALLBACK_DATA = "search_by_location";

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
        $kb = new ReplyKeyboardMarkup(
            [[
                ["text" => "Вінницька обл."],
                ["text" => "Волинська обл."],
                ["text" => "Дніпропетровська обл."],
                ["text" => "Донецька обл."],
                ["text" => "Житомирська обл."],
            ]],
            true
        );

        $this->bot->reply($update->getCallbackQuery()->getMessage(), "Оберіть область із списку:", $kb);
    }
}
