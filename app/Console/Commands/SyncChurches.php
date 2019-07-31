<?php

namespace App\Console\Commands;

use App\Models\Church;
use App\Models\City;
use App\Models\Region;
use App\Services\Bot\Bot;
use App\Services\Bot\DataType\ObjectData;
use Illuminate\Console\Command;

/**
 * Class SyncChurches
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  17.06.2019
 */
class SyncChurches extends Command
{
    /**
     * Command's signature to run
     *
     * @var string
     */
    protected $signature = "sync:churches";

    /**
     * Command's description
     *
     * @var string
     */
    protected $description = "Starts filling/synchronization churches from API to DB";

    public function handle(Bot $bot)
    {
        $offset = 0;
        $limit = 100;

        $progress = $this->output->createProgressBar();
        $progress->setFormat("Processed %current% object(s)");

        // Walk during chains
        do {
            $list = $bot->getStorage()->getObjects($offset, $limit);

            foreach ($list as $objectData) {
                /** @var ObjectData $objectData */
                $progress->advance();

                if ($objectData->type !== ObjectData::TYPE_CHURCH) {
                    $this->info("Object is not church");

                    continue;
                }

                if (empty($objectData->locality)) {
                    $this->info('Object locality is empty');

                    continue;
                }

                if (!$city = $this->findCity($objectData)) {
                    $this->info("City {$objectData->getName()} not found");

                    continue;
                }

                $churchData = [
                    'city_id' => $city->id,
                    'object_id' => $objectData->id,
                    'name' => $objectData->getName(),
                    'address' => $objectData->getAddress(),
                    'latitude' => $objectData->locality->coordinates->latitude,
                    'longitude' => $objectData->locality->coordinates->longitude,
                ];

                if (Church::where('object_id', $objectData->id)->count()) {
                    Church::where('object_id')
                        ->update($churchData);
                } else {
                    Church::create($churchData);
                }
            }

            $offset += count($list);
        } while (count($list));

        $progress->finish();

        $this->info($offset + $limit . " Churches were found and synchronized");
    }

    /**
     * Finds city by object data
     *
     * @param ObjectData $object
     *
     * @return City|null
     */
    public function findCity(ObjectData $object): ?City
    {
        /** @var Region $region */
        static $region;

        $region_name = $object->locality->getRegion();

        // cut word 'область'
        if (strpos($region_name, ' ')) {
            $region_name = substr($region_name, 0, strpos($region_name, ' '));
        }

        if (!$region || $region->name !== $region_name) {
            $region = Region::where('name', $region_name)->first();

            if (!$region) {
                $this->info("Region {$region_name} not found");
                return null;
            }
        }

        $city = $region->cities()
            ->where('name', 'like', "%{$object->locality->getName()}")
            ->first();

        if (!$city) {
            $city = $region->cities()->create([
                'area' => '',
                'name' => $object->locality->getName(),
            ]);
        }

        return $city;
    }
}
