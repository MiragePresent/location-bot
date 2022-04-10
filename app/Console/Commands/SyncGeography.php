<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Region;
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
    protected $description = "Fill/synchronize countries, regions and cities from csv file to DB";

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

        DB::table('countries')
            ->insertOrIgnore([
                'id' => 1,
                'name' => 'Україна',
            ]);

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

            $region_name = trim($segments[0]);

            // Skip Kyiv as region. Use only Kyiv obl.
            if ($region_name === "Київ") {
                continue;
            }

            $area = $segments[1];
            $city_name = $segments[2];

            $region = Region::where('name', $region_name)->first();

            if (!$region) {
                $region = Region::create([
                    'country_id' => 1,
                    'name' => $region_name,
                ]);
            }

            $cityQuery = City::where('region_id', $region->id)
                ->where('name', $city_name)
                ->where('area', $area);

            if (!$cityQuery->count()) {
                City::create([
                    'region_id' => $region->id,
                    'name' => $city_name,
                    'area' => $area,
                ]);
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
