<?php

namespace App\Services\Bot\Handlers;

use TelegramBot\Api\Types\Message;

/**
 * Interface CommandHandlerInterface
 *
 * Uses for implementations which can handle a bot command
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  11.06.2019
 */
interface CommandHandlerInterface
{
    /**
     * Returns command signature
     *
     * @return string
     */
    public function getSignature(): string;

    /**
     * Method that handle the command
     *
     * @param Message $message
     */
    public function handle(Message $message): void;
}
