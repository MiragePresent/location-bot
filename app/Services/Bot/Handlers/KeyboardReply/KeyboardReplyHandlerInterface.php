<?php

namespace App\Services\Bot\Handlers\KeyboardReply;

use TelegramBot\Api\Types\Message;

/**
 * Interface KeyboardReplyHandlerInterface
 *
 * Uses for implementations which can handle an update from user request
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  11.06.2019
 */
interface KeyboardReplyHandlerInterface
{
    /**
     * Checks if handler is suitable for this message reply
     *
     * @param Message $message
     *
     * @return bool
     */
    public static function isSuitable(Message $message): bool;
}
