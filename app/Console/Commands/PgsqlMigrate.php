<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class SyncCitiesAndRegions
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  16.06.2019
 */
class PgsqlMigrate extends Command
{
    /**
     * Command's signature to run
     *
     * @var string
     */
    protected $signature = "pgsql:migrate";

    /**
     * Command's description
     *
     * @var string
     */
    protected $description = "Migrate data to PostgreSQL database";

    public function handle()
    {
        $this->getOutput()->title('Migrating database data');

        $tables = [
            'users',
            'user_locations',
            'countries',
            'regions',
            'cities',
            'churches',
            'church_patches',
            'actions',
            'action_activities'
        ];

        $this->getOutput()->text('Starting data migration...');

        foreach ($tables as $table) {
            $this->printTableInfo($table);
            $this->migrateData($table);
        }

        $this->getOutput()->success('All the data was successfully migrated');

       return 1;
    }

    private function printTableInfo(string $tableName): void
    {
        $size = DB::connection(config('database.default'))->table($tableName)->get()->count();

        $this->getOutput()->text(sprintf('Table: %s (Rows: %d)', $tableName, $size));
    }

    private function migrateData(string $tableName): void
    {
        $pgsql = DB::connection('pgsql');
        $default = DB::connection(config('database.default'));

        $pgsql->beginTransaction();

        try {
            $default
                ->table($tableName)
                ->orderBy('id')
                ->chunk(100, function (Collection $rows) use ($pgsql,$tableName) {
                    $rows = $rows->map(function ($row) {
                        return (array) $row;
                    })->toArray();

                    $pgsql->table($tableName)->insert($rows);
                });

            $pgsql->commit();
        } catch (\Exception $exception) {
            $pgsql->rollBack();

            $this->getOutput()->error(sprintf(
                'Cannot migrate data for %s table. Error: %s',
                $tableName,
                $exception->getMessage()
            ));
        }
    }
}
