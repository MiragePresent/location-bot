<?php

namespace App\Services\Bot\Handlers\Commands;

use App\Models\User;
use App\Services\Bot\Handlers\AbstractCommandHandler;
use TelegramBot\Api\Exception;
use TelegramBot\Api\InvalidArgumentException;
use TelegramBot\Api\Types\Message;

/**
 * Class StartCommand
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  10.06.2019
 */
class StartCommand extends AbstractCommandHandler
{
    /**
     * Command signature
     *
     * @var string
     */
    public const COMMAND_SIGNATURE = "start";

    /**
     * @inheritDoc
     */
    public function getSignature(): string
    {
        return self::COMMAND_SIGNATURE;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function handle(Message $message): void
    {
        $this->bot->log(sprintf(
            "Running start command by: %s",
            $message->getFrom()->toJson()
        ));

        // Save user
        User::createFromTelegramUser($message->getFrom());

        // ⛪
        $church = emoji("\xE2\x9B\xAA");

        $text = "Привіт!\n" .
            "Я був створений для того, щоб допомогти тобі знайти церкву {$church}";

        $this->bot->reply($message, $text);

        // Show help message
        HelpCommand::dispatch($this->bot, $message);
    }
}
