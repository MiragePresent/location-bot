<?php

namespace App\Services\Bot;

use TelegramBot\Api\BotApi;

/**
 * Class Bot
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  07.06.2019
 */
class Bot
{
    /**
     * Telegram bot API wrapper
     *
     * @var BotApi
     */
    protected $api;

    public function __construct(string $token)
    {
        $this->api = new BotApi($token);
    }
}
