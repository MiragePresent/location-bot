<?php

namespace App\Services\Bot\Handlers\KeyboardReply;

use App\Models\Church;
use App\Models\City;
use App\Services\Bot\Answer\SelectOptionAnswer;
use App\Services\Bot\Handlers\AbstractUpdateHandler;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use TelegramBot\Api\Types\Update;
use TelegramBot\Api\Types\Message;

/**
 * Class ShowByCityReply
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  11.06.2019
 */
class ShowByCityReply extends AbstractUpdateHandler implements KeyboardReplyHandlerInterface
{
    /**
     * @inheritDoc
     */
    public static function isSuitable(Message $message): bool
    {
        /** @var City $city */
        $city = Cache::remember(
            md5("city_{$message->getText()}"),
            City::CACHE_LIFE_TIME,
            function () use ($message) {
                return City::has('churches')
                    ->where('name', $message->getText())
                    ->first();
            }
        );

        return ! is_null($city);
    }

    /**
     * @inheritDoc
     */
    public function handle(Update $update): void
    {
        $this->bot->log(sprintf(
            "Show addresses in city: %s \nFor: %s",
            $update->getMessage()->getText(),
            $update->getMessage()->getFrom()->toJson()
        ));

        /** @var City $city */
        $city = Cache::remember(
            md5("city_{$update->getMessage()->getText()}"),
            City::CACHE_LIFE_TIME,
            function () use ($update) {
                return City::has('churches')
                    ->where('name', $update->getMessage()->getText())
                    ->orderBy('name')
                    ->first();
            }
        );

        /** @var Church[]|Collection $churches */
        $churches = Cache::remember(
            "churches_{$city->id}",
            Church::CACHE_LIFE_TIME,
            function () use ($city) {
                return $city->churches;
            }
        );

        if ($churches->count() === 1) {
            $update->getMessage()->setText($churches->first()->name);
            ShowAddressReply::dispatch($this->bot, $update);

            return;
        }

        $churches = $churches->map(function (Church $church) {
            return [[ "text" => $church->name ]];
        })->toArray();
        $answer = new SelectOptionAnswer(trans("bot.messages.text.specify_a_church"), $churches);

        $this->bot->sendTo($update->getMessage(), $answer);
    }
}
