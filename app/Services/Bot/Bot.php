<?php

namespace App\Services\Bot;

use App\Services\Bot\Handlers\CallbackQuery\SearchByListUpdateHandler;
use App\Services\Bot\Handlers\Commands\CommandHandlerInterface;
use App\Services\Bot\Handlers\Commands\CommandStartHandler;
use App\Services\Bot\Handlers\KeyboardReply\KeyboardReplyHandlerInterface;
use App\Services\Bot\Handlers\KeyboardReply\SearchInRegionHandler;
use App\Services\Bot\Handlers\KeyboardReply\ShowAddress;
use App\Services\Bot\Handlers\KeyboardReply\ShowByCity;
use App\Services\Bot\Handlers\UpdateHandlerInterface;
use Closure;
use Illuminate\Log\Logger;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Client;
use TelegramBot\Api\Exception;
use TelegramBot\Api\InvalidArgumentException;
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
     * Logger
     *
     * @var Logger
     */
    protected $logger;

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
        SearchInRegionHandler::class,
        ShowByCity::class,
        ShowAddress::class,
    ];

    public function __construct(Client $client, BotApi $api, Logger $logger)
    {
        $this->client = $client;
        $this->api = $api;
        $this->logger = $logger;

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
        $this->log("Processing update: {$update->getUpdateId()}");

        $handler = null;
        if ($this->isCallbackQuery($update)) {
            if ($update->getCallbackQuery()->getData() === SearchByListUpdateHandler::CALLBACK_DATA) {
                $handler = new SearchByListUpdateHandler($this);
            }
        } elseif ($this->isMessage($update)) {
            foreach ($this->replyHandlers as $handlerClass) {
                /** @var KeyboardReplyHandlerInterface $handlerClass */
                if ($handlerClass::isSuitable($update->getMessage()->getText())) {
                    $handler = new $handlerClass($this);
                }
            }
        }

        if ($handler instanceof UpdateHandlerInterface) {
            $handler->handle($update);
        } else {
            $this->log("Handler not found. \nUpdate: {$update->toJson()}");
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
     * Log bot activity
     *
     * @param string      $message
     * @param null|string $level
     */
    public function log(string $message, string $level = null)
    {
        if (is_null($level) || !method_exists($this->logger, $level)) {
            $level = "info";
        }

        $this->logger->{$level}($message);
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
    public function isMessage(Update $update): bool
    {
        return !empty($update->getMessage());
    }
}
