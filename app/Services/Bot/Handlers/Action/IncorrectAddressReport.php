<?php

namespace App\Services\Bot\Handlers\Action;

use App\Models\Church;
use App\Services\Bot\Answer\ConfirmAddressReportAnswer;
use App\Services\Bot\Answer\TextAnswer;
use App\Services\Bot\Bot;
use App\Services\Bot\Handlers\CallbackQuery\CancelActions;
use App\Services\Bot\Tool\UpdateTree;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\Update;

/**
 * Class IncorrectAddressReport
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  25.08.2019
 */
class IncorrectAddressReport extends AbstractActionHandler
{
    /**
     * @var string
     */
    public const ACTION_KEY = "address_report";

    public const NUMBER_OF_STEPS = 2;

    public const ACTION_DESCRIPTION = "Ask user for actual address if it's incorrect";

    public function getKey(): string
    {
        return static::ACTION_KEY;
    }

    public function getSteps(): int
    {
        return static::NUMBER_OF_STEPS;
    }

    protected function getStageHandler(int $stage)
    {
        $handlers = [
            // asking for street and house number
            0 => [$this, "askStreetAddress"],
            1 => [$this, 'sendConfirmationMessage'],
        ];

        return $handlers[$stage] ?? null;
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
        $msg = new TextAnswer(
            trans(
                "bot.messages.text.ask_for_the_address_correction",
                ["current_address" => $this->getChurch()->address]
            ),
            new InlineKeyboardMarkup([[[
                "text" => trans("bot.interface.button.cannot_help"),
                "callback_data" => CancelActions::CALLBACK_DATA,
            ]]])
        );

        $bot->getApi()->editMessageText($chatId, $message->getMessageId(), $message->getText());

        $bot->sendTo($chatId, $msg);

        $this->getModel()->increaseStage();
        $this->getBot()->log(sprintf("Action %s [stage: %d] is completed", $this->getKey(), $stage));
    }

    /**
     * @inheritDoc
     */
    public function sendConfirmationMessage(Bot $bot, Update $update): void
    {
        $message = UpdateTree::getMessage($update);

        $bot->sendTo(
            $message->getChat()->getId(),
            new ConfirmAddressReportAnswer($message->getText(), $this->action->arguments["object_id"])
        );
    }

    /**
     * @return Church
     */
    private function getChurch(): Church
    {
        /** @var Church $church */
        $church = Church::where("object_id", $this->action->arguments["object_id"])->first();

        return $church;
    }
}
