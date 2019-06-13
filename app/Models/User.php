<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Model User
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  12.06.2019
 *
 * @property int                 $id
 * @property int                 $telegram_id
 * @property string              $username
 * @property string              $lang
 * @property Carbon              $created_at
 * @property Carbon              $updated_at
 *
 * @property-read UserLocation[] $locations
 */
class User extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'telegram_id',
        'username',
        'first_name',
        'lang',
        'created_at',
        'updated_at'
    ];

    // RELATIONS

    /**
     * User locations relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function locations()
    {
        return $this->hasMany(UserLocation::class);
    }
}
