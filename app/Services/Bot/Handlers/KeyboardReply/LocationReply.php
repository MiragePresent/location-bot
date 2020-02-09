<?php

namespace App\Services\Bot\Handlers\KeyboardReply;

use App\Models\Church;
use App\Services\Bot\Handlers\AbstractUpdateHandler;
use App\Services\Bot\Answer\AddressAnswer;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;

/**
 * Class FindByLocation
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  12.06.2019
 */
class LocationReply extends AbstractUpdateHandler implements KeyboardReplyHandlerInterface
{
    /**
     * @inheritDoc
     */
    public static function isSuitable(Message $message): bool
    {
        return !empty($message->getLocation()) && empty($message->getVenue());
    }

    /**
     * @inheritDoc
     */
    public function handle(Update $update): void
    {
        // Log message
        $this->bot->log("Searching church by location. User: %s", $update->getMessage()->toJson());

        $location = $update->getMessage()->getLocation();
        $this->bot->getUser()->saveLocation($location->getLatitude(), $location->getLongitude());

        Church::nearest($location->getLatitude(), $location->getLongitude())
            ->take(3)
            ->get()
            ->each(function (Church $church) use ($update, $location) {
                $object = $this->bot->getStorage()->getObject($church->object_id);
                $distance = $this->calculateDistance(
                    $church->latitude,
                    $church->longitude,
                    $location->getLatitude(),
                    $location->getLongitude()
                );

                $this->bot->sendTo(
                    $update->getMessage()->getChat()->getId(),
                    new AddressAnswer($object, $distance)
                );
            });
    }

    /**
     * Calculate distance from one point to another (in km)
     *
     * @param float $aLat
     * @param float $aLng
     * @param float $bLat
     * @param float $bLng
     *
     * @return float
     *
     * @link https://gist.github.com/statickidz/8a2f0ce3bca9badbf34970b958ef8479
     */
    private function calculateDistance(float $aLat, float $aLng, float $bLat, float $bLng): float
    {
        return 6371 * acos(
            cos(deg2rad($aLat))
            * cos(deg2rad($bLat))
            * cos(deg2rad($bLng) - deg2rad($aLng))
            + sin(deg2rad($aLat))
            * sin(deg2rad($bLat))
        );
    }
}
