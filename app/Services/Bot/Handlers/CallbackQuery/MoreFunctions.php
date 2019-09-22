<?php

namespace App\Services\Bot\Handlers\CallbackQuery;

use App\Services\Bot\Handlers\AbstractUpdateHandler;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\Update;

/**
 * Class PutReportButtons
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  26.08.2019
 */
class MoreFunctions extends AbstractUpdateHandler implements CallbackQueryHandlerInterface
{
    /**
     * Callback identity name
     *
     * @var string
     */
    public const CALLBACK_DATA = 'more_functions';

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
        return false !== strpos($callbackData, static::CALLBACK_DATA);
    }

    public function handle(Update $update): void
    {
        $objectId = $this->getObjectId($update->getCallbackQuery()->getData());

        $buttons[] = [
            "text" => trans("bot.interface.button.wrong_address"),
            "callback_data" => StartAddressReport::CALLBACK_DATA . '_' . $objectId,
        ];
        $buttons[] = [
            "text" => trans("bot.interface.button.back"),
            "callback_data" => RemoveReportButtons::CALLBACK_DATA . '_' . $objectId,
        ];

        $message = $update->getCallbackQuery()->getMessage();
        $chatId = $message->getChat()->getId();
        $this->bot->getApi()->editMessageReplyMarkup(
            $chatId,
            $message->getMessageId(),
            new InlineKeyboardMarkup([$buttons])
        );
    }

    /**
     * Finds church object ID within callback data string
     *
     * @param string $callbackData
     *
     * @return string
     */
    private function getObjectId(string $callbackData): string
    {
        return str_replace(static::CALLBACK_DATA . "_", "", $callbackData);
    }
}
