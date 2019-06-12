<?php

namespace App\Services\Bot\Handlers;

use App\Services\Bot\Bot;
use TelegramBot\Api\Types\Update;

/**
 * Class AbstractHandler
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  11.06.2019
 */
abstract class AbstractUpdateHandler implements UpdateHandlerInterface
{
    /**
     * Bot service instance
     *
     * @var Bot
     */
    protected $bot;

    /**
     * AbstractHandler constructor.
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
     * @param Update $update
     */
    public static function dispatch(Bot $bot, Update $update)
    {
        $self = new static($bot);
        $self->handle($update);
    }
}
