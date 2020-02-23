<?php

namespace App\Models;

use App\Services\Bot\Exception\UpdateParseException;
use App\Services\Bot\Tool\UpdateTree;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use TelegramBot\Api\Types\Update;

/**
 * Model User
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  12.06.2019
 *
 * @property int                 $id
 * @property int                 $telegram_id
 * @property int                 $chat_id
 * @property string              $username
 * @property string              $first_name
 * @property string              $lang
 * @property Carbon              $created_at
 * @property Carbon              $updated_at
 *
 * @property-read UserLocation[] $locations
 * @property-read Action[] $action
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
        'chat_id',
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

    /**
     * User actions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function actions()
    {
        return $this->hasMany(User::class)->orderBy("created_at", "desc");
    }

    /**
     * Finds user by telegram ID
     *
     * @param int $telegramId
     *
     * @return User|null
     */
    public static function findByTelegramId(int $telegramId): ?User
    {
        return static::where('telegram_id', $telegramId)->first();
    }

    /**
     * Return internal user by telegram update
     *
     * @param Update $update
     *
     * @return User
     */
    public static function getByUpdate(Update $update): User
    {
        $tUser = UpdateTree::getUser($update);
        $user = static::findByTelegramId($tUser->getId());
        $chatId = null;

        try {
            $chatId = UpdateTree::getChat($update)->getId();
        } catch (UpdateParseException $exception) {
            // Ignore exception. Chat data can be empty when user uses bot via inline bot
        }

        if ($user instanceof User) {
            $user->update([
                'username' => $tUser->getUsername(),
                'first_name' => $tUser->getFirstName(),
                'chat_id' => $chatId ?? $user->chat_id,
            ]);
        } else {
            $user = static::create([
                'telegram_id' => $tUser->getId(),
                'chat_id' => $chatId,
                'username' => $tUser->getUsername(),
                'first_name' => $tUser->getFirstName(),
                'last_name' => $tUser->getLastName(),
                'lang' => $tUser->getLanguageCode() ?: config('bot.default_lang', 'uk'),
            ]);
        }

        return $user;
    }

    /**
     * Save user location
     *
     * @param float $latitude
     * @param float $longitude
     */
    public function saveLocation(float $latitude, float $longitude)
    {
        $this->locations()->create(compact('latitude', 'longitude'));
    }
}
