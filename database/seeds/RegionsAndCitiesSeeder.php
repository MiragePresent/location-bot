<?php

use App\Models\City;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionsAndCitiesSeeder extends Seeder
{

    public function run()
    {
        DB::select("INSERT IGNORE INTO `countries` (`id`, `name`) VALUES (1, 'Україна')");

        $filepath = database_path('source/houses.csv');
        $handle = fopen($filepath, "r");

        $isFirst = true;

        while(!feof($handle)) {
            $line = fgets($handle);

            $this->command->info($line);

            if ($isFirst) {
                $isFirst = false;
                continue;
            }

            $segments = explode(";", $line);

            $region_name = trim($segments[0]) . " обл.";
            $area = $segments[1];
            $city_name = $segments[2];

            /** @var Region $region */
            $region = Region::where('name', $region_name)->first();

            if (!$region) {
                $region = Region::create([
                    'country_id' => 1,
                    'name' => $region_name,
                ]);
            }

            $city = City::where('region_id', $region->id)
                ->where('name', $city_name)
                ->where('area', $area)
                ->first();

            if (!$city) {
                City::create([
                    'region_id' => $region->id,
                    'name' => $city_name,
                    'area' => $area,
                ]);
            }
        }

    }
}
