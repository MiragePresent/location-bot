<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\App\Providers\ConfigServiceProvider::class);
        $this->app->register(\App\Providers\BotApiServiceProvider::class);
        $this->app->register(\App\Providers\BotServiceProvider::class);
        $this->app->register(\App\Providers\SdaStorageServiceProvider::class);
        $this->app->register(\Laravel\Scout\ScoutServiceProvider::class);
        $this->app->register(\Jcf\Geocode\GeocodeServiceProvider::class);

        // Development tools
        if (env('APP_ENV') !== 'production') {
            $this->app->register(\Laravel\Tinker\TinkerServiceProvider::class);
            $this->app->instance('path.storage', app()->basePath() . DIRECTORY_SEPARATOR . 'storage');
            $this->app->instance('path.config', app()->basePath() . DIRECTORY_SEPARATOR . 'config');

        }
    }
}
