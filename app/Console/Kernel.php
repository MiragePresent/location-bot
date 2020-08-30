<?php

namespace App\Console;

use App\Console\Commands;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

use Nord\Lumen\Elasticsearch\Console\CreateCommand as CreateIndexCommand;
use Nord\Lumen\Elasticsearch\Console\DeleteCommand as DeleteIndexCommand;

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

        CreateIndexCommand::class,
        DeleteIndexCommand::class,
        Commands\IndexLocationsCommand::class,
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
