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

    /**
     * User that sends request
     *
     * @var User
     */
    protected $user;

    protected $commands = [
        StartCommand::class,
        HelpCommand::class,
        FindCommand::class,
    ];

    protected $replyHandlers = [
        LocationReply::class,
        FindInRegionReply::class,
        ShowByCityReply::class,
        ShowAddressReply::class,
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
