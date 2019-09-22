<?php

namespace App\Services\Bot\Handlers\Action;

use App\Models\Church;
use App\Services\Bot\Answer\ConfirmAddressReportAnswer;
use App\Services\Bot\Answer\TextAnswer;
use App\Services\Bot\Bot;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;

/**
 * Class IncorrectAddressReport
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  25.08.2019
 */
class IncorrectAddressReport extends AbstractActionHandler implements ConfirmableActionInterface
{
    /**
     * @var string
     */
    public const ACTION_KEY = "address_report";

    public const NUMBER_OF_STEPS = 1;

    public const ACTION_DESCRIPTION = "Ask user for actual address if it's incorrect";

    public function getKey(): string
    {
        return static::ACTION_KEY;
    }

    public function getSteps(): int
    {
        return static::NUMBER_OF_STEPS;
    }

    /**
     * @inheritDoc
     */
    public function sendConfirmationMessage(Message $message)
    {
        $this->bot->sendTo(
            $message->getChat()->getId(),
            new ConfirmAddressReportAnswer($message->getText(), $this->action->arguments["object_id"])
        );
    }

    protected function getStageHandler(int $stage): callable
    {
        $handlers = [
            // asking for street and house number
            0 => [$this, "askStreetAddress"],
        ];

        return $handlers[$stage];
    }

    /**
     * @param Bot    $bot
     * @param Update $update
     * @param int    $stage
     *
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\InvalidArgumentException
     */
    public function askStreetAddress(Bot $bot, Update $update, int $stage): void
    {
        $message = $update->getCallbackQuery()->getMessage();
        $chatId = $message->getChat()->getId();
        $msg = new TextAnswer(trans("bot.messages.text.ask_for_the_address_correction", [
            "current_address" => $this->getChurch()->address
        ]));

        $bot->getApi()->editMessageText($chatId, $message->getMessageId(), $message->getText());

        $bot->sendTo($chatId, $msg);
    }

    /**
     * @return Church
     */
    private function getChurch(): Church
    {
        return Church::where("object_id", $this->action->arguments["object_id"])->first();
    }
}
