<?php

namespace App\Services\Bot\Handlers\CallbackQuery;

use App\Models\Action;
use App\Services\Bot\Handlers\AbstractUpdateHandler;
use TelegramBot\Api\Types\Update;

/**
 * Class CancelActions
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  08.09.2019
 */
class CancelActions extends AbstractUpdateHandler implements CallbackQueryHandlerInterface
{
    public const CALLBACK_DATA = "cancel_actions";

    /**
     * @inheritDoc
     */
    public function getCallbackData(): string
    {
        return static::CALLBACK_DATA;
    }

    /**
     * @inheritDoc
     */
    public static function isSuitable(string $callbackData): bool
    {
        return static::CALLBACK_DATA === $callbackData;
    }

    public function handle(Update $update): void
    {
        Action::whereUserId($this->bot->getUser()->id)
            ->isActive()
            ->update([
                "is_canceled" => true,
                "cancel_reason" => Action::CANCEL_REASON_BY_USER,
            ]);

        // Delete buttons
        $message = $update->getCallbackQuery()->getMessage();
        $this->getBot()->getApi()->editMessageReplyMarkup(
            $message->getChat()->getId(),
            $message->getMessageId()
        );

        // Notify user about cancellation
        $this->bot->getApi()->answerCallbackQuery(
            $update->getCallbackQuery()->getId(),
            trans("bot.messages.text.canceled")
        );
    }
}
