<?php

namespace App\Services\Bot;

use App\Models\User;
use App\Services\Bot\Handlers\CallbackQuery\FindByList;
use App\Services\Bot\Handlers\CallbackQuery\FindByLocation;
use App\Services\Bot\Handlers\CommandHandlerInterface;
use App\Services\Bot\Handlers\Commands\FindCommand;
use App\Services\Bot\Handlers\Commands\HelpCommand;
use App\Services\Bot\Handlers\Commands\StartCommand;
use App\Services\Bot\Handlers\InlineSearch;
use App\Services\Bot\Handlers\KeyboardReply\IncorrectMessage;
use App\Services\Bot\Handlers\KeyboardReply\LocationReply;
use App\Services\Bot\Handlers\KeyboardReply\KeyboardReplyHandlerInterface;
use App\Services\Bot\Handlers\KeyboardReply\FindInRegionReply;
use App\Services\Bot\Handlers\KeyboardReply\ShowAddressReply;
use App\Services\Bot\Handlers\KeyboardReply\ShowByCityReply;
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
     * Cache life time for inline mode results
     *
     * @var int
     */
    public const CACHE_INLINE_MODE_LIFE_TIME = 60 * 60;
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

    /**
     * SDA API client
     *
     * @var StorageClient
     */
    protected $storage;

    /**
     * Logger
     *
     * @var Logger
     */
    protected $logger;

    /**
     * User that sends request
     *
     * @var User
     */
    protected $user;

    /**
     * Bot commands handlers
     *
     * @var array
     */
    protected $commands = [
        StartCommand::class,
        HelpCommand::class,
        FindCommand::class,
    ];

    /**
     * Bot messages handlers
     *
     * @var array
     */
    protected $replyHandlers = [
        LocationReply::class,
        FindInRegionReply::class,
        ShowByCityReply::class,
        ShowAddressReply::class,
    ];

    /**
     * Bot service constructor.
     *
     * @param Client        $client
     * @param BotApi        $api
     * @param StorageClient $storage
     * @param Logger        $logger
     */
    public function __construct(Client $client, BotApi $api, StorageClient $storage, Logger $logger)
    {
        $this->client = $client;
        $this->api = $api;
        $this->logger = $logger;
        $this->storage = $storage;

        $this->registerCommands();
    }

    /**
     * Returns telegram bot client
     *
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Returns telegram bot api
     *
     * @return BotApi
     */
    public function getApi(): BotApi
    {
        return $this->api;
    }

    /**
     * Returns SDA api with stored churches data
     *
     * @return StorageClient
     */
    public function getStorage(): StorageClient
    {
        return $this->storage;
    }

    /**
     * User that sent request
     *
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Returns bot username
     *
     * @return string
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function getUsername(): string
    {
        return $this->getApi()->getMe()->getUsername();
    }

    /**
     * This method handles all updates (except commands)
     *
     * @param Update $update
     */
    public function processUpdate(Update $update)
    {
        $this->log("Processing update: {$update->getUpdateId()}");

        $handler = null;

        if ($this->isInlineQuery($update)) {
            $this->user = User::findByTelegramId($update->getInlineQuery()->getFrom()->getId());

            $handler = new InlineSearch($this);
            $handler->handle($update);
        } elseif ($this->isCallbackQuery($update)) {
            $this->user = User::findByTelegramId($update->getCallbackQuery()->getFrom()->getId());

            if ($update->getCallbackQuery()->getData() === FindByList::CALLBACK_DATA) {
                $handler = new FindByList($this);
            } elseif ($update->getCallbackQuery()->getData() === FindByLocation::CALLBACK_DATA) {
                $handler = new FindByLocation($this);
            }
        } elseif ($this->isMessage($update)) {
            $this->user = User::findByTelegramId($update->getMessage()->getFrom()->getId());

            foreach ($this->replyHandlers as $handlerClass) {
                /** @var KeyboardReplyHandlerInterface $handlerClass */
                if ($handlerClass::isSuitable($update->getMessage())) {
                    $handler = new $handlerClass($this);

                    break;
                }
            }
        }

        if (! ($handler instanceof UpdateHandlerInterface)) {
            $this->log("Handler not found. \nUpdate: {$update->toJson()}");
            $handler = new IncorrectMessage($this);
        }

        $handler->handle($update);
    }

    /**
     * Send reply to chat
     *
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

    /**
     * Run web hooks listening
     *
     * @throws \TelegramBot\Api\InvalidJsonException
     */
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
