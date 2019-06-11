<?php

namespace App\Services\Bot\Handlers\KeyboardReply;

use App\Services\Bot\Handlers\AbstractUpdateHandler;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use TelegramBot\Api\Types\Update;

/**
 * Class SearchInRegionHandler
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  11.06.2019
 */
class SearchInRegionUpdateHandler
    extends AbstractUpdateHandler
    implements KeyboardReplyHandlerInterface
{
    /**
     * @inheritDoc
     */
    public static function isSuitable(string $reply): bool
    {
        $regions = [
            "Вінницька обл.",
            "Волинська обл.",
            "Дніпропетровська обл.",
            "Донецька обл.",
            "Житомирська обл."
        ];

        return in_array($reply, $regions);
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
        ]], true);

        $this->bot->reply($update->getMessage(), "Вкажіть, будь ласка, місто:", $kb);
    }
}
