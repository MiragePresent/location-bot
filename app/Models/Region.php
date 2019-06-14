<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Region
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  12.06.2019
 *
 * @property int           $id
 * @property string        $name
 *
 * @property-read City[]   $cities
 * @property-read Church[] $churches
 */
class Region extends Model
{
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
    protected $fillable = [ 'name' ];

    // RELATIONS

    /**
     * City relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cities()
    {
        return $this->hasMany(City::class);
    }

    /**
     * Churches relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function churches()
    {
        return $this->hasManyThrough(Church::class, City::class);
    }
}
