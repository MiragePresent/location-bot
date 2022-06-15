<?php

namespace App\Services\Bot\Handlers\Commands;

use App\Services\Bot\Answer\HumanitarianHelpIntroAnswer;
use App\Services\Bot\Handlers\AbstractCommandHandler;
use App\Services\Bot\Answer\TextAnswer;
use App\Services\Bot\Handlers\CallbackQuery\GetHumanitarianHelp;
use App\Services\Bot\Tracker\StatsTrackerInterface;
use TelegramBot\Api\Exception;
use TelegramBot\Api\InvalidArgumentException;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\Message;

/**
 * Class StartCommand
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  10.06.2019
 */
class StartCommand extends AbstractCommandHandler
{
    /**
     * Command signature
     *
     * @var string
     */
    public const COMMAND_SIGNATURE = "start";

    /**
     * @inheritDoc
     */
    public function getSignature(): string
    {
        return self::COMMAND_SIGNATURE;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function handle(Message $message): void
    {
        $this->bot->getStatsTracker()->setRequestType(StatsTrackerInterface::REQUEST_TYPE_SERVICE_MESSAGE);

        $this->bot->log(sprintf(
            "Running start command by: %s",
            $message->getFrom()->toJson()
        ));

        $this->bot->sendTo($message->getChat()->getId(), new TextAnswer(trans("bot.messages.text.start")));

        // Show help message
        HelpCommand::dispatch($this->bot, $message);

        $this->bot->sendTo($message->getChat()->getId(), new HumanitarianHelpIntroAnswer());
    }
}
