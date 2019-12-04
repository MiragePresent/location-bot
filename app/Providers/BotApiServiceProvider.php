<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use TelegramBot\Api\BotApi;

/**
 * Class BotApiServiceProvider
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  01.12.2019
 */
class BotApiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(BotApi::class, function () {
            return new BotApi(config('bot.token'));
        });
    }
}
