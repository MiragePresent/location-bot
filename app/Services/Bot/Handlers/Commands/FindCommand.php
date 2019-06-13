<?php

namespace App\Services\Bot\Handlers\Commands;

use App\Services\Bot\Handlers\AbstractCommandHandler;
use App\Services\Bot\Handlers\CallbackQuery\FindByListHandler;
use App\Services\Bot\Handlers\CallbackQuery\FindByLocationHandler;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\Message;

/**
 * Class FindCommandInterface
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  13.06.2019
 */
class FindCommand extends AbstractCommandHandler
{
    /**
     * Command signature
     *
     * @var string
     */
    public const COMMAND_SIGNATURE = 'find';

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
            "Running find command by: %s",
            $message->getFrom()->toJson()
        ));

        $text = "Ти можеш вибрати церкву із списку, " .
            "або просто відправити мені своє розташування і я знайду найближчу до тебе церкву";

        $byList = [
            "text" => "Вибрати із списку",
            "callback_data" => FindByListHandler::CALLBACK_DATA,
        ];
        $byLocation = [
            "text" => "Знайти поблизу",
            "callback_data" => FindByLocationHandler::CALLBACK_DATA,
        ];

        $keyboard = new InlineKeyboardMarkup([[ $byList, $byLocation ]]);

        $this->bot->reply($message, $text, $keyboard);
    }
}
