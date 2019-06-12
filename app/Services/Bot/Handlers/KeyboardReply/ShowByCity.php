<?php

namespace App\Services\Bot\Handlers\KeyboardReply;

use App\Services\Bot\Handlers\AbstractUpdateHandler;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use TelegramBot\Api\Types\Update;
use TelegramBot\Api\Types\Message;

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
    public static function isSuitable(Message $message): bool
    {
        return in_array($message->getText(), [
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
        ]], true, true);

        $this->bot->reply($update->getMessage(), "Яка саме церква тебе цікавить?", $kb);
    }
}
