<?php

namespace App\Services\Bot\Handlers\KeyboardReply;

use App\Services\Bot\Handlers\AbstractUpdateHandler;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use TelegramBot\Api\Types\Update;

/**
 * Class SearchInRegionHandler
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  11.06.2019
 */
class FindInRegionHandler extends AbstractUpdateHandler implements KeyboardReplyHandlerInterface
{
    /**
     * @inheritDoc
     */
    public static function isSuitable(Message $message): bool
    {
        $regions = [
            "Вінницька обл.",
            "Волинська обл.",
            "Дніпропетровська обл.",
            "Донецька обл.",
            "Житомирська обл."
        ];

        return in_array($message->getText(), $regions);
    }

    /**
     * @inheritDoc
     */
    public function handle(Update $update): void
    {
        $kb = new ReplyKeyboardMarkup([[
            "Вінниця",
            "Луцьк",
            "Дніпро",
            "Донецьк",
            "Житомир",
        ]], true, true);

        $this->bot->reply($update->getMessage(), "Вкажи, будь ласка, місто", $kb);
    }
}
