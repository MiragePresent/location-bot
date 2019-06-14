<?php

namespace App\Services\Bot\Handlers\Commands;

use App\Services\Bot\Handlers\AbstractCommandHandler;
use TelegramBot\Api\Types\Message;

/**
 * Class HelpCommand
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  13.06.2019
 */
class HelpCommand extends AbstractCommandHandler
{
    /**
     * Command signature
     *
     * @var string
     */
    public const COMMAND_SIGNATURE = 'help';

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
        $this->bot->log(sprintf(
            "Running help command by: %s",
            $message->getFrom()->toJson()
        ));

        $winking_face = emoji("\xF0\x9F\x98\x89"); // 😉
        $list_dot = emoji("\xE2\x96\xAA"); // ▪

        $text = "Ось мої основні можливості:\n" .

            // /find command description
            "{$list_dot} Для пошуку викликай команду /" . FindCommand::COMMAND_SIGNATURE .
            " і просто слідуй інструкціям.\n" .

            // Inline mode description
            "{$list_dot} Також ти можеш скористатись пошуком звернувшись до мене @" . $this->bot->getUsername() .
            " та вказавши населений пункт. \n" .

            // /help command description
            "{$list_dot} для отримання довідки використовуй команду /" . static::COMMAND_SIGNATURE . "\n".
            "\n" .
            "Надіюсь стану тобі в нагоді {$winking_face}";

        $this->bot->reply($message, $text);
    }
}
