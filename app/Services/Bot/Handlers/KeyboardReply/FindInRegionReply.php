<?php

namespace App\Services\Bot\Handlers\KeyboardReply;

use App\Models\City;
use App\Models\Region;
use App\Services\Bot\Handlers\AbstractUpdateHandler;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use TelegramBot\Api\Types\Update;

/**
 * Class SearchInRegionHandler
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  11.06.2019
 */
class FindInRegionReply extends AbstractUpdateHandler implements KeyboardReplyHandlerInterface
{
    /**
     * @inheritDoc
     */
    public static function isSuitable(Message $message): bool
    {
        /** @var Region $region */
        $region = Cache::remember(
            md5("region_{$message->getText()}"),
            Region::CACHE_LIFE_TIME,
            function () use ($message) {
                return Region::where('name', $message->getText())->first();
            }
        );

        return ! is_null($region);
    }

    /**
     * @inheritDoc
     */
    public function handle(Update $update): void
    {
        $this->bot->log(sprintf(
            "Search in region: %s \nFor: %s",
            $update->getMessage()->getText(),
            $update->getMessage()->getFrom()->toJson()
        ));

        /** @var Region $region */
        $region = Cache::remember(
            md5("region_{$update->getMessage()->getText()}"),
            Region::CACHE_LIFE_TIME,
            function () use ($update) {
                return Region::where('name', $update->getMessage()->getText())->first();
            }
        );

        /** @var City[]|Collection $cities */
        $cities = Cache::remember("cities_{$region->id}", City::CACHE_LIFE_TIME, function () use ($region) {
            return $region->cities()
                ->has('churches')
                ->orderBy('name')
                ->get();
        });

        $keyboard = new ReplyKeyboardMarkup(
            $cities->map(function (City $city) {
                return [[ "text" => $city->name ]];
            })->toArray(),
            true,
            true
        );

        $this->bot->reply($update->getMessage(), "Вкажи, будь ласка, місто", $keyboard);
    }
}
