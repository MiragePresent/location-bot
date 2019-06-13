<?php

namespace App\Services\Bot\Handlers;

use App\Services\Bot\Bot;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;

/**
 * Class AbstractCommandHandler
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  13.06.2019
 */
abstract class AbstractCommandHandler implements CommandHandlerInterface
{
    /**
     * Bot service
     *
     * @var Bot
     */
    protected $bot;

    /**
     * AbstractCommandHandler constructor.
     *
     * @param Bot $bot
     */
    public function __construct(Bot $bot)
    {
        $this->bot = $bot;
    }

    /**
     * Run handler statically
     *
     * @param Bot    $bot
     * @param Message $message
     */
    public static function dispatch(Bot $bot, Message $message)
    {
        $self = new static($bot);
        $self->handle($message);
    }
}
