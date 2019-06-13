<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model City
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  12.06.2019
 *
 * @property int           $id
 * @property int           $region_id
 * @property string        $name
 *
 * @property-read Region   $region
 * @property-read Church[] $churches
 */
class City extends Model
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
    protected $fillable = [
        'region_id',
        'name',
    ];

    // RELATIONS

    /**
     * Region relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Churches relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function churches()
    {
        return $this->hasMany(Church::class);
    }
}
