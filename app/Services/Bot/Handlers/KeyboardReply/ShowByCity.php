<?php

namespace App\Services\Bot\Handlers\KeyboardReply;

use App\Services\Bot\Handlers\AbstractUpdateHandler;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use TelegramBot\Api\Types\Update;

/**
 * Class ShowByCity
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  11.06.2019
 */
class ShowByCity extends AbstractUpdateHandler implements KeyboardReplyHandlerInterface
{
    /**
     * @inheritDoc
     */
    public static function isSuitable(string $reply): bool
    {
        return in_array($reply, [
            "Вінниця",
            "Луцьк",
            "Дніпро",
            "Донецьк",
            "Житомир",
        ]);
    }

    /**
     * @inheritDoc
     */
    public function handle(Update $update): void
    {
        $kb = new ReplyKeyboardMarkup([[
            "Луцьк І",
            "Ковчег",
            "Луцьк ІІІ",
        ]], true);

        $this->bot->reply($update->getMessage(), ">>", $kb);
    }
}
