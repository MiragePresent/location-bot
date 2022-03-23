<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Model UserLocation
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  12.06.2019
 *
 * @property int       $id
 * @property float     $latitude
 * @property float     $longitude
 * @property Carbon    $created_at
 *
 * @property-read User $user
 */
class UserLocation extends Model
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
        'user_id',
        'latitude',
        'longitude',
        'created_at',
    ];

    protected $dates = [ 'created_at' ];

    // RELATIONS

    /**
     * User relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
