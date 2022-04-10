<?php

namespace App\Services\Bot;

use App\Models\Action;
use App\Models\User;
use App\Services\Bot\Handlers\Action\IncorrectAddressReport;
use App\Services\Bot\Handlers\CallbackQuery;
use App\Services\Bot\Handlers\CommandHandlerInterface;
use App\Services\Bot\Handlers\Commands;
use App\Services\Bot\Handlers\InlineSearch;
use App\Services\Bot\Handlers\KeyboardReply;
use App\Services\Bot\Answer\AnswerInterface;
use App\Services\SdaStorage\StorageClient;
use Closure;
use Illuminate\Log\Logger;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Client;
use TelegramBot\Api\Exception;
use TelegramBot\Api\InvalidArgumentException;
use TelegramBot\Api\Types\Chat;
use TelegramBot\Api\Types\Location;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;
use Throwable;

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
     * Message parse format
     *
     * @var string
     */
    public const PARSE_FORMAT_MARKDOWN = 'markdown';

    /**
     * The "typing" action key
     *
     * @link https://core.telegram.org/bots/api#sendchataction
     * @var string
     */
    private const ACTION_TYPING = "typing";

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
        Commands\StartCommand::class,
        Commands\HelpCommand::class,
        Commands\FindCommand::class,
    ];

    /**
     * Bot messages handlers
     *
     * @var array
     */
    protected $replyHandlers = [
        KeyboardReply\LocationReply::class,
        KeyboardReply\FindInRegionReply::class,
        KeyboardReply\ShowAddressReply::class,
        KeyboardReply\DefaultTextReplyHandler::class,
    ];

    /**
     * Bot callback queries handlers
     *
     * @var array
     */
    protected $callbackQueries = [
        CallbackQuery\FindByList::class,
        CallbackQuery\FindByLocation::class,
        CallbackQuery\MoreFunctions::class,
        CallbackQuery\HelpProjectAlert::class,
        CallbackQuery\RemoveReportButtons::class,
        CallbackQuery\StartAddressReport::class,
        CallbackQuery\RollbackAddressReport::class,
        CallbackQuery\ConfirmAddressReport::class,
        CallbackQuery\CancelActions::class,
    ];

    /**
     * Bot service constructor.
     *
     * @param Client        $client
     * @param BotApi        $api
     * @param StorageClient $storage
     * @param Logger        $logger
     */
    public function __construct(
        Client $client,
        BotApi $api,
        StorageClient $storage,
        Logger $logger
    ) {
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
        $botUsername = $this->getApi()->getMe()->getUsername() ?: config('bot.username_fallback');

        return str_replace("_", "\_", $botUsername);
    }

    /**
     * This method handles all updates (except commands)
     *
     * @param Update $update
     */
    public function processUpdate(Update $update)
    {
        $this->log("Processing update: {$update->toJson()}");

        $handler = null;

        $this->user = User::getByUpdate($update);

        if (is_null($this->user)) {
            $this->log('Update cannot be processed. User was not detected', 'warning');

            return;
        }

        try {
            if ($this->isCommand($update)) {
                $this->setTyping($update);
                $this->closeActions();

                return;
            } elseif ($this->isInlineQuery($update)) {
                $handler = new InlineSearch($this);
            } elseif ($this->isCallbackQuery($update)) {
                foreach ($this->callbackQueries as $handlerClass) {
                    /** @var CallbackQuery\CallbackQueryHandlerInterface $handlerClass */
                    if ($handlerClass::isSuitable($update->getCallbackQuery()->getData())) {
                        $handler = new $handlerClass($this);

                        break;
                    }
                }
            } elseif ($this->isMessage($update)) {
                // TODO: find right way of detecting inline mode replies

                // ignore not text or location messages
                if (!$update->getMessage()->getText() && !($update->getMessage()->getLocation() instanceof Location)) {
                    return;
                }

                $this->setTyping($update);

                // Close actions if message has location
                // Actions don't support location yet
                if ($update->getMessage()->getLocation() instanceof Location) {
                    $this->closeActions();
                }

                if ($this->isThereActiveAction()) {
                    /** @var Action $action */
                    $action = Action::query()
                        ->where("user_id", $this->getUser()->id)->latest()->first();

                    if ($action->key === IncorrectAddressReport::ACTION_KEY) {
                        $actionHandler = new IncorrectAddressReport($action, $this);
                        $actionHandler->handleStage($update, $action->stage);
                    }

                    return;
                }

                foreach ($this->replyHandlers as $handlerClass) {
                    /** @var KeyboardReply\KeyboardReplyHandlerInterface $handlerClass */
                    if ($handlerClass::isSuitable($update->getMessage())) {
                        $handler = new $handlerClass($this);

                        break;
                    }
                }
            }

            app()->call([$handler, 'handle'], ['update' => $update]);
        } catch (Throwable $throwable) {
            $this->log($throwable->getMessage(), 'error');
            $handler = new KeyboardReply\IncorrectMessage($this);
            $handler->handle($update);

            $this->log(sprintf(
                "Handler error. Error message: %s \nUpdate[%s]: Text: %s",
                $throwable->getMessage(),
                $update->getUpdateId(),
                $this->isMessage($update) ? $update->getMessage()->getText() : 'NOT_MESSAGE_UPDATE'
            ), 'error');
        }
    }

    /**
     * Send reply to chat
     *
     * @param int|Message     $to
     * @param AnswerInterface $message
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function sendTo($to, AnswerInterface $message)
    {
        $chatId = $to instanceof Message ? $to->getChat()->getId() : $to;

        $this->getApi()->sendMessage(
            $chatId,
            $message->getText(),
            self::PARSE_FORMAT_MARKDOWN,
            false,
            null,
            $message->getMarkup()
        );
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
    public function log(string $message, string $level = null, array $context = [])
    {
        if (is_null($level) || !method_exists($this->logger, $level)) {
            $level = "info";
        }

        $this->logger->{$level}($message, $context);
    }

    /**
     * Trigger typing
     *
     * @param Update $update
     */
    public function setTyping(Update $update)
    {
        $chat = $update->getMessage()->getChat();

        if ($chat instanceof Chat) {
            $this->getApi()->sendChatAction($chat->getId(), self::ACTION_TYPING);
        }
    }

    public function getSupportInfo(): array
    {
        return [
            'channel' => [
                'name' => config('bot.support.channel.name'),
                'link' => config('bot.support.channel.link'),
            ]
        ];
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
     * Determines whether message is command
     *
     * @param Update $update
     *
     * @return bool
     */
    private function isCommand(Update $update): bool
    {
        if (!empty($update->getMessage())) {
            foreach ($this->commands as $commandClass) {
                /** @var CommandHandlerInterface $command */
                $command = new $commandClass($this);

                if ('/' . $command->getSignature() === $update->getMessage()->getText()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Determines whether update is inline query
     *
     * @param Update $update
     *
     * @return bool
     */
    private function isInlineQuery(Update $update): bool
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

    /**
     * Check if there is open multi step action
     *
     * @return bool
     */
    private function isThereActiveAction(): bool
    {
        return Action::isActive()
                ->where("user_id", $this->getUser()->id)
                ->count() > 0;
    }

    /**
     * Closes the all users actions
     */
    private function closeActions()
    {
        Action::isActive()
            ->where("user_id", $this->getUser()->id)
            ->update([
                "is_canceled" => true,
                "cancel_reason" => Action::CANCEL_REASON_BY_BOT,
            ]);
    }
}
