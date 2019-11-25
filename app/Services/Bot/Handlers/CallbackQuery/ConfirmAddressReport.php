<?php

namespace App\Services\Bot\Handlers\CallbackQuery;

use App\Models\Action;
use App\Services\Bot\Handlers\AbstractUpdateHandler;
use App\Services\Bot\Handlers\Action\IncorrectAddressReport;
use TelegramBot\Api\Types\Update;

/**
 * Class ConfirmAddressReport
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  08.09.2019
 */
class ConfirmAddressReport extends AbstractUpdateHandler implements CallbackQueryHandlerInterface
{
    public const CALLBACK_DATA = "confirm_address_report";

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
        return false !== strpos($callbackData, static::CALLBACK_DATA);
    }

    /**
     * @inheritDoc
     */
    public function handle(Update $update): void
    {
        /** @var Action $action */
        $action = Action::whereUserId($this->bot->getUser()->id)
            ->where("key", IncorrectAddressReport::ACTION_KEY)
            ->latest()
            ->first();

        if (!$action instanceof Action) {
            throw new \InvalidArgumentException("There is no active actions.");
        }

        $action->confirm()->done();

        $this->getBot()->getApi()->editMessageReplyMarkup(
            $update->getCallbackQuery()->getMessage()->getChat()->getId(),
            $update->getCallbackQuery()->getMessage()->getMessageId()
        );

        $this->getBot()->getApi()->answerCallbackQuery(
            $update->getCallbackQuery()->getId(),
            trans("bot.messages.text.thank_you_for_helping"),
            true
        );
    }
}
