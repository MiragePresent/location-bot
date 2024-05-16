<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read null|int $id
 * @property null|int $user_id
 * @property null|string $poll_name
 * @property null|string $answer
 *
 * @property-read ?User $user
 */
class PollAnswer extends Model
{
    protected $table = 'user_poll_answers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "user_id",
        "poll_name",
        "answer",
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
