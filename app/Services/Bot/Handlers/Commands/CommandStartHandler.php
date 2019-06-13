<?php

namespace App\Services\Bot\Handlers\Commands;

use App\Services\Bot\Bot;
use App\Services\Bot\Handlers\CallbackQuery\SearchByListUpdateHandler;
use TelegramBot\Api\Exception;
use TelegramBot\Api\InvalidArgumentException;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\Message;

/**
 * Class CommandStartHandler
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  10.06.2019
 */
class CommandStartHandler implements CommandHandlerInterface
{
    /**
     * Bot service instance
     *
     * @var Bot
     */
    protected $bot;

    /**
     * CommandStartHandler constructor.
     *
     * @param Bot $bot
     */
    public function __construct(Bot $bot)
    {
        $this->bot = $bot;
    }

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

        $byLocation = [
            "text" => "Знайти поблизу",
            "callback_data" => Bot::CALLBACK_NAME_BY_LOCATION,
        ];

        $byCity = [
            "text" => "Вибрати із списку",
            "callback_data" => SearchByListUpdateHandler::CALLBACK_DATA,
        ];

        $keyboard = new InlineKeyboardMarkup([[ $byLocation, $byCity ]]);

        $text = "Привіт!\n" .
            "Я був створений для того, щоб допомогти тобі знайти церкву.\n" .
            "Вибери зручний спосіб пошуку:";

        $this->bot->reply($message, $text, $keyboard);
    }
}
