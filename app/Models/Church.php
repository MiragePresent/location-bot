<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Church
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  12.06.2019
 *
 * @property int       $id
 * @property int       $city_id
 * @property string    $name
 * @property string    $address
 * @property float     $latitude
 * @property float     $longitude
 *
 * @property-read City $city
 */
class Church extends Model
{
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
        'name',
        'address',
        'latitude',
        'longitude',
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
}
