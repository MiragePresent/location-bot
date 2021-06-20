<?php

namespace App\Services\Bot\Handlers\Commands;

use App\Services\Bot\Handlers\AbstractCommandHandler;
use App\Services\Bot\Answer\TextAnswer;
use App\Services\Bot\Handlers\CallbackQuery\HelpProjectAlert;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\Message;

/**
 * Class HelpCommand
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  13.06.2019
 */
class HelpCommand extends AbstractCommandHandler
{
    /**
     * Command signature
     *
     * @var string
     */
    public const COMMAND_SIGNATURE = 'help';

    /**
     * @inheritDoc
     */
    public function getSignature(): string
    {
        return static::COMMAND_SIGNATURE;
    }

    /**
     * @inheritDoc
     */
    public function handle(Message $message): void
    {
        $this->bot->log(sprintf(
            "Running help command by: %s",
            $message->getFrom()->toJson()
        ));

        $help = [
            'text' => trans("bot.interface.button.help_project"),
            'callback_data' => HelpProjectAlert::CALLBACK_DATA,
        ];

        $answer = new TextAnswer(
            trans("bot.messages.text.help", ["bot_username" => $this->bot->getUsername()]),
            new InlineKeyboardMarkup([[$help]])
        );

        $this->bot->sendTo($message->getChat()->getId(), $answer);
    }
}
