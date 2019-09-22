<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ActionActivity
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  12.08.2019
 *
 * @property int $id
 * @property int $action_id
 * @property int $stage Action stage activity
 * @property string $data Activity log data
 * @property int $status Stage status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Action $action Related action
 */
class ActionActivity extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "action_id",
        "stage",
        "data",
        "status",
    ];

    protected $casts = [
        "data" => "array",
    ];

    // Relations

    /**
     * Action relation declaration
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function action()
    {
        return $this->belongsTo(Action::class);
    }
}
