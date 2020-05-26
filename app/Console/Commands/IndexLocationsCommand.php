<?php

namespace App\Console\Commands;

use App\Models\Church;
use Nord\Lumen\Elasticsearch\Console\IndexCommand;

class IndexLocationsCommand extends IndexCommand
{
    public const INDEX_NAME = 'locations';

    protected $signature = 'elastic:import:' . self::INDEX_NAME;

    protected $description = 'Indexes all persons into the search index';

    /**
     * @return array
     */
    public function getData()
    {
        return Church::with('city')->get();
    }

    /**
     * @return string
     */
    public function getIndex()
    {
        return 'locations';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return null;
    }

    /**
     * @param Church $church
     *
     * @return array
     */
    public function getItemBody($church)
    {
        return [
            'name' => $church->name,
            'city' => $church->city->name,
            'address' => $church->address,
            'location' => [
                'lat' => $church->latitude,
                'lon' => $church->longitude,
            ],
        ];
    }

    /**
     * @param Church $church
     *
     * @return string
     */
    public function getItemId($church)
    {
        return $church->id;
    }

    /**
     * @param mixed $item
     *
     * @return string|null
     */
    public function getItemParent($item)
    {
        return null;
    }

    protected function getProgressBarRedrawFrequency()
    {
        return 10;
    }
}
