<?php

namespace App\Services\Bot\Handlers\CallbackQuery;

use App\Services\Bot\Answer\FindChurchAnswer;
use App\Services\Bot\Answer\TextAnswer;
use App\Services\Bot\Handlers\AbstractUpdateHandler;
use App\Services\Bot\Tool\UpdateTree;
use App\Services\Bot\Tracker\StatsTrackerInterface;
use TelegramBot\Api\Types\Update;

/**
 * Class HumanitarianHelp
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  15.06.2022
 */
class GetHumanitarianHelp extends AbstractUpdateHandler implements CallbackQueryHandlerInterface
{

    /**
     * Callback identity name
     *
     * @var string
     */
    public const CALLBACK_DATA = "get_humanitarian_help";

    /**
     * @inheritDoc
     */
    public function getCallbackData(): string
    {
        return self::CALLBACK_DATA;
    }

    /**
     * @inheritDoc
     */
    public static function isSuitable(string $callbackData): bool
    {
        return self::CALLBACK_DATA === $callbackData;
    }

    public function handle(Update $update)
    {
        $this->bot->getStatsTracker()->setRequestType(StatsTrackerInterface::REQUEST_HUMANITARIAN_HELP);

        $this->bot->log(
            sprintf(
                "CallbackQuery: %s \nFrom: %s",
                $update->getCallbackQuery()->getData(),
                $update->getCallbackQuery()->getFrom()->toJson()
            )
        );

        $chatId = UpdateTree::getChat($update)->getId();
        $churchHelpAnswer = new FindChurchAnswer(
            "Для отримання допомоги ви можете звернутись за адресою найближчої до вас церкви " . emoji("\xE2\x9B\xAA"), // ⛪
        );
        $adraHelpAnswer = new TextAnswer(
            "Також ви можете звернутись в Адвентистське Агенство Допомоги та Розвитку в Україні \"[ADRA Ukraine](https://www.facebook.com/ADRA.Ukraine/)\" " . PHP_EOL .
            "*Адреса* " . PHP_EOL .
            "м. Київ " . PHP_EOL .
            "вулиця Лариси Руденко, 3" . PHP_EOL .
            "*Телефон*" . PHP_EOL .
            "+38 067 333 1752" . PHP_EOL .
            "+38 093 170 6739" . PHP_EOL .
            "+38 050 615 5750"
        );

        $this->bot->sendTo($chatId, $churchHelpAnswer);
        $this->bot->sendTo($chatId, $adraHelpAnswer);
    }
}
