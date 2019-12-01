<?php

namespace App\Providers;

use App\Services\SdaStorage\StorageClient;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\ServiceProvider;

/**
 * Class SdaApiStorage
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  01.12.2019
 */
class SdaStorageServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(StorageClient::class, function () {
            return new StorageClient(config('bot.storage_api'), new GuzzleClient());
        });
    }
}
