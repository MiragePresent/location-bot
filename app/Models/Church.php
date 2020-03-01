<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;

/**
 * Model Church
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  12.06.2019
 *
 * @property int       $id
 * @property int       $city_id
 * @property int       $object_id
 * @property string    $name
 * @property string    $address
 * @property float     $latitude
 * @property float     $longitude
 *
 * @property-read City $city
 * @property-read ChurchPatch[]|null $patches Address and location patches
 * @property-read null|float $distance Distance between user and church (in km)
 *
 * @method static Builder nearest(float $latitude, float $longitude)  Finds the nearest churches
 * @method static Builder where($column, $condition, $value = null)  Finds the nearest churches
 */
class Church extends Model
{
    use Searchable;

    /**
     * Cache life time in seconds (a week)
     *
     * @var int
     */
    public const CACHE_LIFE_TIME = 7 * 24 * 60 * 60;

    /**
     * {@inheritDoc}
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'city_id',
        'object_id',
        'name',
        'address',
        'latitude',
        'longitude',
        'created_at',
        'updated_at',
    ];

    // RELATIONS

    /**
     * Region relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Patches relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function patches()
    {
        return $this->hasMany(ChurchPatch::class);
    }

    /**
     * @param Builder $query
     * @param float   $latitude
     * @param float   $longitude
     *
     * @return Builder
     * @link https://gist.github.com/statickidz/8a2f0ce3bca9badbf34970b958ef8479
     */
    public function scopeNearest(
        Builder $query,
        float $latitude,
        float $longitude
    ): Builder {
        return $query
            ->select('*')
            ->addSelect(DB::raw("(
                      6371 * acos (
                      cos ( radians({$latitude}) )
                      * cos( radians( latitude ) )
                      * cos( radians( longitude ) - radians({$longitude}) )
                      + sin ( radians({$latitude}) )
                      * sin( radians( latitude ) )
                    )
                ) AS `distance`
            "))
            ->orderByRaw(
                "(
                    (latitude - {$latitude}) * (latitude - {$latitude})) 
                    + ((longitude - {$longitude}) * (longitude - {$longitude})
                ) ASC"
            );
    }

    /**
     * @inheritDoc
     */
    public function searchableAs()
    {
        return config('scout.indices.churches');
    }

    /**
     * @inheritDoc
     */
    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'city' => $this->city->name,
            'address' => $this->address,
        ];
    }
}
