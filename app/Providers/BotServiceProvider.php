<?php

namespace App\Providers;

use App\Services\Bot\Bot;
use App\Services\Bot\Tracker\DBStatsRepository;
use App\Services\Bot\Tracker\DBStatsTracker;
use App\Services\Bot\Tracker\StatsRepositoryInterface;
use App\Services\Bot\Tracker\StatsTrackerInterface;
use App\Services\SdaStorage\StorageClient;
use Illuminate\Support\Facades\Log;
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
        $this->app->bind(StatsTrackerInterface::class, function () {
            $logger = Log::channel('bot_activity');

            return new DBStatsTracker($logger);
        });

        $this->app->bind(StatsRepositoryInterface::class, function () {
            return new DBStatsRepository();
        });

        $this->app->bind(Bot::class, function () {
            $client = new Client(config('bot.token'));
            $api = $this->app->get(BotApi::class);
            $storage = $this->app->get(StorageClient::class);
            $tracker = $this->app->get(StatsTrackerInterface::class);
            $logger = Log::channel('bot_activity');

            return new Bot($client, $api, $storage, $tracker, $logger);
        });
    }
}
