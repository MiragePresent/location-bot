<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repository;

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
        $this->app->register(\Jcf\Geocode\GeocodeServiceProvider::class);
        $this->app->register(\Nord\Lumen\Elasticsearch\ElasticsearchServiceProvider::class);

        $this->app->bind(Repository\LocationRepository::class, Repository\LocationRepository::class);

        // Development tools
        if (env('APP_ENV') !== 'production') {
            $this->app->register(\Laravel\Tinker\TinkerServiceProvider::class);
            $this->app->instance('path.storage', app()->basePath() . DIRECTORY_SEPARATOR . 'storage');
            $this->app->instance('path.config', app()->basePath() . DIRECTORY_SEPARATOR . 'config');

        }
    }
}
