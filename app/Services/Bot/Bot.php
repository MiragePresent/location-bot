<?php

namespace App\Services\Bot;

use App\Services\Bot\Handlers\CallbackQuery\FindByListHandler;
use App\Services\Bot\Handlers\CallbackQuery\FindByLocationHandler;
use App\Services\Bot\Handlers\Commands\CommandHandlerInterface;
use App\Services\Bot\Handlers\Commands\CommandStartHandler;
use App\Services\Bot\Handlers\KeyboardReply\FindTheNearestHandler;
use App\Services\Bot\Handlers\KeyboardReply\KeyboardReplyHandlerInterface;
use App\Services\Bot\Handlers\KeyboardReply\FindInRegionHandler;
use App\Services\Bot\Handlers\KeyboardReply\ShowAddress;
use App\Services\Bot\Handlers\KeyboardReply\ShowByCity;
use App\Services\Bot\Handlers\UpdateHandlerInterface;
use Closure;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Client;
use TelegramBot\Api\Exception;
use TelegramBot\Api\InvalidArgumentException;
use TelegramBot\Api\Types\Inline\QueryResult\Venue;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;

/**
 * Class Bot
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  07.06.2019
 */
class Bot
{
    /**
     * Message type when it's a command
     *
     * @var string
     */
    public const MESSAGE_ENTITY_TYPE_BOT_COMMAND = 'bot_command';

    public const CALLBACK_NAME_BY_CITY = "inline_callback_by_city";
    public const CALLBACK_NAME_BY_LOCATION = "inline_callback_by_location";

    public const COMMAND_START = 'start';

    /**
     * Telegram bot API wrapper
     *
     * @var Client
     */
    protected $client;

    /**
     * Bot API
     *
     * @var BotApi
     */
    protected $api;

    protected $chatId;

    protected $commands = [
       CommandStartHandler::class,
    ];

    protected $replyHandlers = [
        FindTheNearestHandler::class,
        FindInRegionHandler::class,
        ShowByCity::class,
        ShowAddress::class,
    ];

    public function __construct(string $token)
    {
        $this->client = new Client($token);
        $this->api = new BotApi($token);

        $this->registerCommands();
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getApi(): BotApi
    {
        return $this->api;
    }

    public function processUpdate(Update $update)
    {
        $handler = null;

        if ($this->isInlineQuery($update)) {
            $church = new Venue(623847, 50.7419858, 25.2792952, "Луцьк I", "Владимирская ул., 89б, Луцк, Волынская область, 45624");

            $this->api->answerInlineQuery($update->getInlineQuery()->getId(), [$church]);
        } elseif ($this->isCallbackQuery($update)) {
            if ($update->getCallbackQuery()->getData() === FindByListHandler::CALLBACK_DATA) {
                $handler = new FindByListHandler($this);
            } elseif ($update->getCallbackQuery()->getData() === FindByLocationHandler::CALLBACK_DATA) {
                $handler = new FindByLocationHandler($this);
            }
        } elseif ($this->isMessage($update)) {
            foreach ($this->replyHandlers as $handlerClass) {
                /** @var KeyboardReplyHandlerInterface $handlerClass */
                if ($handlerClass::isSuitable($update->getMessage())) {
                    $handler = new $handlerClass($this);
                }
            }
        }

        if ($handler instanceof UpdateHandlerInterface) {
            $handler->handle($update);
        }
    }

    /**
     * @param int|string|Message $to
     * @param      $text
     * @param null $kb
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function reply($to, $text, $kb = null)
    {
        if ($to instanceof Message) {
            $this->getApi()->sendMessage($to->getChat()->getId(), $text, null, false, null, $kb);
        } else {
            $this->getApi()->sendMessage($to, $text, null, false, null, $kb);
        }
    }

    public function run()
    {
        $this->getClient()->run();
    }

    /**
     * Registers all bot command to the client
     */
    private function registerCommands()
    {
        foreach ($this->commands as $commandHandler) {

            /** @var CommandHandlerInterface $handler */
            $handler = new $commandHandler($this);

            $this->getClient()->command(
                $handler->getSignature(),
                Closure::fromCallable([$handler, "handle"])
            );
        }
    }

    /**
     * Determines whether update is inline query
     *
     * @param Update $update
     *
     * @return bool
     */
    public function isInlineQuery(Update $update): bool
    {
        return !empty($update->getInlineQuery());
    }

    /**
     * Determines whether update is inline button event
     *
     * @param Update $update
     *
     * @return bool
     */
    private function isCallbackQuery(Update $update): bool
    {
        return !empty($update->getCallbackQuery());
    }

    /**
     * Determines whether update is message
     *
     * @param Update $update
     *
     * @return bool
     */
    private function isMessage(Update $update): bool
    {
        return !empty($update->getMessage());
    }
}
