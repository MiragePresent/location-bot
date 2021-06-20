<?php

namespace App\Services\Bot\Handlers\CallbackQuery;

use App\Services\Bot\Answer\HelpProjectAnswer;
use App\Services\Bot\Handlers\AbstractUpdateHandler;
use App\Services\Bot\Tool\UpdateTree;
use TelegramBot\Api\Types\Update;

/**
 * Class HelpProjectAlert
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  20.06.2021
 */
class HelpProjectAlert extends AbstractUpdateHandler implements CallbackQueryHandlerInterface
{
    public const CALLBACK_DATA = 'help_project';

    public function getCallbackData(): string
    {
        return self::CALLBACK_DATA;
    }

    public static function isSuitable(string $callbackData): bool
    {
        return $callbackData === self::CALLBACK_DATA;
    }

    public function handle(Update $update)
    {
        $support = $this->getBot()->getSupportInfo();
        $answer = new HelpProjectAnswer($support['channel']['name'], $support['channel']['link']);

        $this->getBot()->sendTo(UpdateTree::getChat($update)->getId(), $answer);
    }
}
