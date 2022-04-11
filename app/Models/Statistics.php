<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Statistics
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  10.04.2022
 *
 * @property-read int       $id
 * @property int            $user_id
 * @property string         $request_type
 * @property string         $request_status
 * @property int            $sent_messages  Number of sent messages during request
 * @property int            $failures       Number of failures during request
 * @property Carbon         $created_at
 *
 * @property-read User|null $user
 */
class Statistics extends Model
{
    protected $table = 'statistics';

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
