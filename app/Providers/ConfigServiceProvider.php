<?php

namespace App\Providers;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application;

/**
 * Registers and loads configurations into the app
 *
 * @property-read Application $app Lumen Application instance
 */
class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Path to config files
     *
     * @var string
     */
    private const CONFIG_PATH = 'config';

    /**
     * @inheritDoc
     *
     * @throws FileNotFoundException
     */
    public function boot()
    {
        $path = $this->app->basePath() . '/' . self::CONFIG_PATH;

        if (!is_dir($path)) {
            throw new FileNotFoundException(
                "The config folder is missing." .
                "\nCreate it on the root folder of your project and add the config files there."
            );
        }

        collect(scandir($path))->each(function ($file) {
            $this->app->configure(basename($file, '.php'));
        });
    }
}
