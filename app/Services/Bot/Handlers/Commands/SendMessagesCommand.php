<?php

namespace App\Services\Bot\Handlers\Commands;

use App\Services\Bot\Answer\TextAnswer;
use App\Services\Bot\Handlers\AbstractCommandHandler;
use App\Services\Bot\Handlers\KeyboardReply\IncorrectMessage;
use Illuminate\Support\Facades\Artisan;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;

/**
 * Class StatsCommand
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  10.04.2022
 */
class SendMessagesCommand extends AbstractCommandHandler
{
    public const COMMAND_SIGNATURE = 'send-messages';

    /**
     * @inheritDoc
     */
    public function getSignature(): string
    {
        return static::COMMAND_SIGNATURE;
    }

    /**
     * @inheritDoc
     */
    public function handle(Message $message): void
    {
        if (!$this->bot->getUser()->isAdmin()) {
            $update = new Update();
            $update->setMessage($message);

            IncorrectMessage::dispatch($this->bot, $update);

            return;
        }

        Artisan::call("poll:send-messages basic-feedback");

        $this->bot->sendTo(
            $message->getChat()->getId(), 
            new TextAnswer("Sending basic-feedback request messages")
        );
    }
}
