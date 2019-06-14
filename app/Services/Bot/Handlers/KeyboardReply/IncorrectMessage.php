<?php

namespace App\Services\Bot\Handlers\KeyboardReply;

use App\Services\Bot\Handlers\AbstractUpdateHandler;
use App\Services\Bot\Handlers\Commands\HelpCommand;
use TelegramBot\Api\Types\Update;

/**
 * Class IncorrectMessage
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  14.06.2019
 */
class IncorrectMessage extends AbstractUpdateHandler
{
    /**
     * @inheritDoc
     */
    public function handle(Update $update): void
    {
        $text = "Вибач, не можу опрацювати твій запит " . emoji("\xF0\x9F\x98\xA8") . PHP_EOL .
            "Можливо ти хотів би переглянути ще раз мої основні можливості, тоді використай команду /" .
            HelpCommand::COMMAND_SIGNATURE;

        $this->bot->reply($update->getMessage(), $text);
    }
}
