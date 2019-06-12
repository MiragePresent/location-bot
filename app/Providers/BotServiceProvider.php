<?php

namespace App\Providers;

use App\Services\Bot\Bot;
use Illuminate\Support\ServiceProvider;

class BotServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Bot::class, function () {
            return new Bot(config('bot.token'));
        });
    }
}
