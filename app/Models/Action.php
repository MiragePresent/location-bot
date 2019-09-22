<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Action
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  12.08.2019
 *
 * @property int $id
 * @property int $user_id User relation
 * @property string $key Action identity
 * @property string $description Action description
 * @property array $arguments Action arguments
 * @property int $steps Number of action steps
 * @property int $stage Current step
 * @property bool $is_confirmed Confirmation status
 * @property bool $is_done
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read User $user Related user
 * @property-read ActionActivity[]|null $activities Action activities data
 *
 * @method static Builder|self whereUserId(int $user_id)
 * @method static Builder|self isActive()
 * @method static Builder|self latest()
 */
class Action extends Model
{
    public const CANCEL_REASON_BY_BOT = 1;
    public const CANCEL_REASON_BY_USER = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "user_id",
        "key",
        "description",
        "arguments",
        "steps",
        "stage",
        "is_confirmed",
        "is_done",
        "is_canceled",
        "cancel_reason",
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        "is_confirmed" => "bool",
        "is_done" => "bool",
        "is_canceled" => "bool",
        "arguments" => "array",
    ];

    // Relations

    /**
     * User relation declaration
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Activities relation declaration
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activities()
    {
        return $this->hasMany(ActionActivity::class);
    }

    // Helpers

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeIsActive($query)
    {
        return $query->where("is_done", false)
            ->where("is_canceled", false);
    }

    /**
     * Increase action stage counter
     *
     * @return Action
     */
    public function increaseStage(): self
    {
        $this->update(['stage' => $this->stage + 1]);

        return $this;
    }

    /**
     * Increase action stage counter
     *
     * @return Action
     */
    public function decreaseStage(): self
    {
        $this->update(['stage' => $this->stage - 1]);

        return $this;
    }

    /**
     * Mark action as confirmed
     *
     * @return Action
     */
    public function confirm(): self
    {
        $this->update(["is_confirmed" => true]);

        return $this;
    }

    /**
     * Mark action as done
     *
     * @return Action
     */
    public function done(): self
    {
        $this->update(["is_done" => true]);

        return $this;
    }
}
