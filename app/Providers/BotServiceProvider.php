<?php

namespace App\Providers;

use Apix\Log\Logger\File;
use App\Services\Bot\Bot;
use App\Services\SdaStorage\StorageClient;
use Illuminate\Log\Logger;
use Illuminate\Support\ServiceProvider;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Client;

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

            $client = new Client(config('bot.token'));
            $api = $this->app->get(BotApi::class);
            $storage = $this->app->get(StorageClient::class);

            $fileLogger = new File(storage_path('logs/bot_activity.log'));
            $fileLogger->setMinLevel("debug")
                ->setCascading(false)
                ->setDeferred(true);

            $logger = new Logger($fileLogger);

            return new Bot($client, $api, $storage, $logger);
        });
    }
}
