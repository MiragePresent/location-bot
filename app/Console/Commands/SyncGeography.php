<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class SyncCitiesAndRegions
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  16.06.2019
 */
class SyncGeography extends Command
{
    /**
     * Command's signature to run
     *
     * @var string
     */
    protected $signature = "sync:geography";

    /**
     * Command's description
     *
     * @var string
     */
    protected $description = "Starts filling/synchronization countries, regions and cities from csv file to DB";

    /**
     * Source file
     *
     * @var string
     */
    protected $sourcePath;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        $this->sourcePath = database_path('source/houses.csv');
    }

    public function handle()
    {
        $progress = $this->output->createProgressBar($this->getCountLines());
        $progress->setFormat("%message% \n%current%/%max% [%bar%] %percent%%");

        DB::select("INSERT IGNORE INTO `countries` (`id`, `name`) VALUES (1, 'Україна')");

        $handle = fopen($this->sourcePath, "r");

        $isFirst = true;

        while (!feof($handle)) {
            $progress->advance();

            $line = fgets($handle);
            $segments = explode(";", $line);

            if ($isFirst || count($segments) < 3) {
                $isFirst = false;
                continue;
            }

            $region_name = trim($segments[0]) . " обл.";

            if ($region_name === 'Київ обл.') {
                $region_name = 'Київська обл.';
            }

            $area = $segments[1];
            $city_name = $segments[2];

            $region = DB::table('regions')->select('id')->where('name', $region_name)->first();

            if (!$region) {
                DB::select("
                    INSERT IGNORE INTO `regions` (
                        `country_id`,
                        `name`,
                    )
                    VALUES (1, '{$region_name}')
                ");

                $region_id = DB::getPdo()->lastInsertId();
            } else {
                $region_id = $region->id;
            }

            $result = DB::table('cities')
                ->select(DB::raw('count(id) as count'))
                ->where('region_id', $region_id)
                ->where('name', $city_name)
                ->where('area', $area)
                ->limit(1)
                ->first();

            if (!$result->count) {
                DB::select("
                    INSERT IGNORE INTO cities (
                        `region_id`,
                        `name`,
                        `area`
                    ) VALUES ({$region_id}, '{$city_name}', '{$area}')
                ");
            }

            $progress->setMessage(
                "Processing: $region_name, " .
                ($area ? "{$area} р-н, " : '').
                $city_name
            );
        }

        fclose($handle);

        $progress->finish();
    }

    /**
     * Returns number of lines in source file
     *
     * @return int
     */
    private function getCountLines(): int
    {
        exec("wc -l {$this->sourcePath} | awk '{ print $1 }'", $result);

        return (int) $result[0];
    }
}
