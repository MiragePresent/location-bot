<?php

namespace App\Console;

use App\Console\Commands;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\SyncGeography::class,
        Commands\SyncChurches::class,
        Commands\BotSetWebHookCommand::class,
        Commands\PatchChurchImport::class,
        Commands\PatchChurchGenerate::class,
        Commands\LocationsFixCommand::class,
        Commands\SendPollMessages::class,
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
