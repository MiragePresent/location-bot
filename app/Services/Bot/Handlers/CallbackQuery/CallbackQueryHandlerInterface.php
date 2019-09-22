<?php

namespace App\Services\Bot\Handlers\CallbackQuery;

/**
 * Interface UpdateHandlerInterface
 *
 * Uses for implementations which can handle an callback query request
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  11.06.2019
 */
interface CallbackQueryHandlerInterface
{
    /**
     * Callback identity name
     *
     * @return string
     * @link https://core.telegram.org/bots/api#callbackquery
     */
    public function getCallbackData(): string;

    /**
     * Checks if handler is suitable for this callback
     *
     * @param string $callbackData
     *
     * @return bool
     */
    public static function isSuitable(string $callbackData): bool;
}
