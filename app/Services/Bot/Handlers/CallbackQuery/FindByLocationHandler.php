<?php

namespace App\Services\Bot\Handlers\CallbackQuery;

use App\Services\Bot\Handlers\AbstractUpdateHandler;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use TelegramBot\Api\Types\Update;

/**
 * Class FindByLocationHandler
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  12.06.2019
 */
class FindByLocationHandler extends AbstractUpdateHandler implements CallbackQueryHandlerInterface
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
        $kb = new ReplyKeyboardMarkup([[
            ["text" => "Я зараз тут", "request_location" => true]
        ]], true);

        $this->bot->reply($update->getCallbackQuery()->getMessage(), "Де ти зараз знаходишся?", $kb);
    }
}
