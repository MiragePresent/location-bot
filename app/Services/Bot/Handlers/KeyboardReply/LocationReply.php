<?php

namespace App\Services\Bot\Handlers\KeyboardReply;

use App\Models\Church;
use App\Repository\LocationRepository;
use App\Services\Bot\Answer\InaccurateDataWarningAnswer;
use App\Services\Bot\Answer\NoResultsByLocationAnswer;
use App\Services\Bot\Handlers\AbstractUpdateHandler;
use App\Services\Bot\Answer\AddressAnswer;
use App\Services\Bot\Tool\UpdateTree;
use App\Services\Bot\Tracker\StatsTrackerInterface;
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
    public function handle(Update $update, LocationRepository $repository): void
    {
        $this->bot->getStatsTracker()->setRequestType(StatsTrackerInterface::REQUEST_TYPE_ADDRESS);

        $searchRadius = config('bot.search_radius');

        // Log message
        $this->bot->log(sprintf("Searching church by location. User: %s", $update->getMessage()->toJson()));

        $location = $update->getMessage()->getLocation();
        $this->bot->getUser()->saveLocation($location->getLatitude(), $location->getLongitude());

        $repository->findNearBy($searchRadius, $location->getLatitude(), $location->getLongitude(), 3)
            ->tap(function ($results) use ($update, $searchRadius) {
                $answer = count($results) > 0
                    ? new InaccurateDataWarningAnswer()
                    : new NoResultsByLocationAnswer($searchRadius / 1000);

                $this->getBot()->sendTo(UpdateTree::getChat($update)->getId(), $answer);
            })
            ->each(function (Church $church) use ($update, $location) {
                $object = $this->bot->getStorage()->getObject($church->object_id);

                $this->bot->sendTo(
                    $update->getMessage()->getChat()->getId(),
                    new AddressAnswer($church, $object)
                );
            });
    }
}
