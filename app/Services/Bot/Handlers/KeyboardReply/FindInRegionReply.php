<?php

namespace App\Services\Bot\Handlers\KeyboardReply;

use App\Models\City;
use App\Models\Region;
use App\Services\Bot\Answer\SelectOptionAnswer;
use App\Services\Bot\Handlers\AbstractUpdateHandler;
use App\Services\Bot\Tracker\StatsTrackerInterface;
use Illuminate\Support\Facades\Cache;
use TelegramBot\Api\Types\Message;
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

        return $region instanceof Region;
    }

    /**
     * @inheritDoc
     */
    public function handle(Update $update): void
    {
        $this->bot->getStatsTracker()->setRequestType(StatsTrackerInterface::REQUEST_TYPE_INTERFACE_INTERACTION);

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

        /** @var array $cities */
        $cities = Cache::remember("cities_in_region_{$region->id}", City::CACHE_LIFE_TIME, function () use ($region) {
            return $region->cities()
                ->has('churches')
                ->orderBy('name')
                ->get();
        })->map(function (City $city) {
            return [[ "text" => $city->name ]];
        })->toArray();

        $answer = new SelectOptionAnswer(trans("bot.messages.text.specify_a_city"), $cities);

        $this->bot->sendTo($update->getMessage(), $answer);
    }
}
