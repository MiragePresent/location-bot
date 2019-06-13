<?php

namespace App\Services\Bot\Handlers\KeyboardReply;

use App\Models\Church;
use App\Services\Bot\Handlers\AbstractUpdateHandler;
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
            ->each(function (Church $church) use ($update){
                $this->bot->getApi()->sendVenue(
                    $update->getMessage()->getChat()->getId(),
                    $church->latitude,
                    $church->longitude,
                    $church->name . sprintf(" (%01.2f км.)", $church->distance),
                    $church->address
                );
            });
    }
}
