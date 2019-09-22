<?php

namespace App\Services\Bot\Handlers\Action;

use TelegramBot\Api\Types\Message;

interface ConfirmableActionInterface
{
    /**
     * Sends confirmation request
     *
     * @param Message $message
     * @return void
     */
    public function sendConfirmationMessage(Message $message);
}
