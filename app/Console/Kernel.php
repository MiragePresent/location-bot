<?php

namespace App\Console;

use App\Console\Commands\SyncChurches;
use App\Console\Commands\SyncGeography;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use Nord\Lumen\Elasticsearch\Console\CreateCommand;
use Nord\Lumen\Elasticsearch\Console\DeleteCommand;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        SyncGeography::class,
        SyncChurches::class,
        CreateCommand::class,
        DeleteCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
