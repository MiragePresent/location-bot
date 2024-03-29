<?php

namespace App\Services\Bot\Handlers\Commands;

use App\Services\Bot\Answer\TextAnswer;
use App\Services\Bot\Handlers\AbstractCommandHandler;
use App\Services\Bot\Handlers\KeyboardReply\IncorrectMessage;
use App\Services\Bot\Tracker\StatsRepositoryInterface;
use App\Services\Bot\Tracker\StatsTrackerInterface;
use Illuminate\Support\Facades\App;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;

/**
 * Class StatsCommand
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  10.04.2022
 */
class StatsCommand extends AbstractCommandHandler
{
    public const COMMAND_SIGNATURE = 'stats';

    /**
     * @inheritDoc
     */
    public function getSignature(): string
    {
        return static::COMMAND_SIGNATURE;
    }

    /**
     * @inheritDoc
     */
    public function handle(Message $message): void
    {
        if (!$this->bot->getUser()->isAdmin()) {
            $update = new Update();
            $update->setMessage($message);

            IncorrectMessage::dispatch($this->bot, $update);

            return;
        }

        $this->bot->getStatsTracker()->setRequestType(StatsTrackerInterface::REQUEST_TYPE_DEV);

        /** @var StatsRepositoryInterface $statsRepository */
        $statsRepository = App::make(StatsRepositoryInterface::class);
        $chatId = $message->getChat()->getId();

        $generalStats = new TextAnswer(sprintf(
            "*General statistics*\n" .
            "Number of users: %d\n" .
            "Requests number (_since 11.04.2022_): %d\n" .
            "Number of churches found: %d\n" .
            "Errors: %d\n\n" .
            "Humanitarian help requests (_since 15.06.2022_): %d\n",
            $statsRepository->numberOfUsers(),
            $statsRepository->numberOfAllRequests(),
            $statsRepository->numberOfAddressesFound(),
            $statsRepository->numberOfErrors(),
            $statsRepository->numberOfHumanitarianHelpRequests(),
        ));

        $this->bot->sendTo($chatId, $generalStats);

        // TODO: detailed statistics message
    }
}
