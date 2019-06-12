<?php

namespace App\Services\Bot\Handlers\KeyboardReply;

use App\Services\Bot\Handlers\AbstractUpdateHandler;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;

/**
 * Class ShowAddress
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  11.06.2019
 */
class ShowAddress extends AbstractUpdateHandler implements KeyboardReplyHandlerInterface
{
    /**
     * @inheritDoc
     */
    public static function isSuitable(Message $message): bool
    {
        return $message->getText() === "Луцьк І";
    }

    /**
     * @inheritDoc
     */
    public function handle(Update $update): void
    {
        $addr = "вул. Володимирська 89б";

        $kb = new InlineKeyboardMarkup([[
            [ "text" => "Відкрити на карті", "url" => "https://goo.gl/maps/KrxQJzejNp7jsE5t9" ],
        ]]);

        $this->bot->reply($update->getMessage(), $addr, $kb);
    }
}
