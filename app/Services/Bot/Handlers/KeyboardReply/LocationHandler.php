<?php

namespace App\Services\Bot\Handlers\KeyboardReply;

use App\Services\Bot\Handlers\AbstractUpdateHandler;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;

/**
 * Class FindByLocationHandler
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  12.06.2019
 */
class LocationHandler extends AbstractUpdateHandler implements KeyboardReplyHandlerInterface
{
    /**
     * @inheritDoc
     */
    public static function isSuitable(Message $message): bool
    {
        return !empty($message->getLocation()) && empty($message->getVenue());
    }

    /**
     * @inheritDoc
     */
    public function handle(Update $update): void
    {
        $this->bot->reply($update->getMessage(), "Дєржи атвєт");
    }
}
