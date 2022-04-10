<?php

namespace App\Repository;

use App\Models\Church;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class LocationRepository
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  26.05.2020
 */
class LocationRepository
{
    public const DEFAULT_LIMIT = 10;
    public const DEFAULT_OFFSET = 0;

    public function findByText(
        string $text,
        int $limit = self::DEFAULT_LIMIT,
        int $offset = self::DEFAULT_OFFSET
    ): Collection {
        return Church::query()
            ->from('churches as ch')
            ->addSelect([
                "ch.*",
                DB::raw("similarity(ch.name, ?) as church_name_score"),
                DB::raw("similarity(c.name, ?) as city_name_score")
            ])
            ->join('cities as c', 'c.id', 'ch.city_id')
            ->where(function (Builder $query) use ($text) {
                $query->whereRaw("similarity(ch.name, ?) > .2")
                    ->orWhereRaw("similarity(c.name, ?) > .2");
            })
            ->orderByRaw("c.name <-> ?")
            ->orderByRaw("ch.name <-> ?")
            ->orderBy("ch.name")
            ->offset($offset)
            ->limit($limit)
            ->setBindings(array_fill(0, 6, $text))
            ->get();
    }

    /**
     * @param int   $radius     Search radius in meters
     * @param float $latitude
     * @param float $longitude
     * @param int   $limit
     * @param int   $offset
     *
     * @return Collection|Church[]
     */
    public function findNearBy(
        int $radius,
        float $latitude,
        float $longitude,
        int $limit = self::DEFAULT_LIMIT,
        int $offset = self::DEFAULT_OFFSET
    ): Collection {
        return Church::query()
            ->addSelect([
                "*",
                DB::raw("ROUND(earth_distance(ll_to_earth(?,?), ll_to_earth(latitude, longitude))::NUMERIC, 3) AS distance")
            ])
            ->where(function ($query) use ($radius, $latitude, $longitude){
                $query
                    ->whereRaw("earth_box(ll_to_earth(?,?), ?) @> ll_to_earth(latitude, longitude)")
                    ->whereRaw("earth_distance(ll_to_earth(?,?), ll_to_earth(latitude, longitude)) < ?");
            })
            ->addBinding([
                $latitude, $longitude,
                $latitude, $longitude, $radius,
                $latitude, $longitude, $radius,
            ])
            ->orderBy("distance")
            ->limit($limit)
            ->offset($offset)
            ->get();
    }
}
