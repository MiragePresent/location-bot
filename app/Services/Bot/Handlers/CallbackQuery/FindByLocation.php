<?php

namespace App\Services\Bot\Handlers\CallbackQuery;

use App\Services\Bot\Answer\FindByLocationAnswer;
use App\Services\Bot\Handlers\AbstractUpdateHandler;
use TelegramBot\Api\Types\Update;

/**
 * Class FindByLocation
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  12.06.2019
 */
class FindByLocation extends AbstractUpdateHandler implements CallbackQueryHandlerInterface
{
    /**
     * Callback identity name
     *
     * @var string
     */
    public const CALLBACK_DATA = "search_by_location";

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
    public function handle(Update $update): void
    {
        $this->bot->sendTo(
            $update->getCallbackQuery()->getMessage()->getChat()->getId(),
            new FindByLocationAnswer()
        );
    }
}
