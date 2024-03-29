<?php

namespace App\Services\Bot\Handlers\Commands;

use App\Services\Bot\Handlers\AbstractCommandHandler;
use App\Services\Bot\Answer\TextAnswer;
use App\Services\Bot\Tracker\StatsTrackerInterface;
use TelegramBot\Api\HttpException;
use TelegramBot\Api\Types\Message;

/**
 * Class HelpCommand
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  13.06.2019
 */
class HelpCommand extends AbstractCommandHandler
{
    /**
     * Command signature
     *
     * @var string
     */
    public const COMMAND_SIGNATURE = 'help';

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
        $this->bot->getStatsTracker()->setRequestType(StatsTrackerInterface::REQUEST_TYPE_SERVICE_MESSAGE);

        $this->bot->log(sprintf(
            "Running help command by: %s",
            $message->getFrom()->toJson()
        ));

        try {
            $chatId = $message->getChat()->getId();
            $support = $this->bot->getSupportInfo();
            $channel = $support['channel'];

            $answer = new TextAnswer(trans("bot.messages.text.help", [
                'support_channel_link' => $channel['link'],
                'support_channel_name' => $channel['name'],
            ]));

            $this->bot->sendTo($chatId, $answer);
        } catch (HttpException $apiException) {
            $this->bot->log(sprintf(
                "Cannot handle help message answer. \nError: %s", $apiException->getMessage()),
                "error",
                ['code' => $apiException->getCode()]
            );
        }
    }
}
