<?php

namespace App\Services\Bot\Handlers\KeyboardReply;

use App\Services\Bot\Answer\TextAnswer;
use App\Services\Bot\Handlers\AbstractUpdateHandler;
use App\Services\Bot\Tracker\StatsTrackerInterface;
use TelegramBot\Api\Types\Update;

/**
 * Class IncorrectMessage
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  14.06.2019
 */
class IncorrectMessage extends AbstractUpdateHandler
{
    /**
     * @inheritDoc
     */
    public function handle(Update $update): void
    {
        $this->bot->getStatsTracker()->setRequestType(StatsTrackerInterface::REQUEST_TYPE_INTERFACE_INTERACTION);

        $this->bot->sendTo($update->getMessage(), new TextAnswer(trans("bot.messages.text.incorrect_request")));
    }
}
