<?php

namespace App\Services\Bot\Handlers\Commands;

use App\Services\Bot\Handlers\AbstractCommandHandler;
use App\Services\Bot\Answer\TextAnswer;
use TelegramBot\Api\HttpException;
use TelegramBot\Api\Types\Message;

/**
 * Class HelpCommand
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  13.06.2019
 */
class InfoCommand extends AbstractCommandHandler
{
    /**
     * Command signature
     *
     * @var string
     */
    public const COMMAND_SIGNATURE = 'info';

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
            "Running info command by: %s",
            $message->getFrom()->toJson()
        ));

        try {
            $chatId = $message->getChat()->getId();
            $support = $this->bot->getSupportInfo();
            $channel = $support['channel'];

            $this->bot->sendTo($chatId, new TextAnswer(sprintf("This is test info command answer. channel: %s", json_encode($channel))));

            $answer = new TextAnswer(trans("bot.messages.text.help", [
                "bot_username" => $this->bot->getUsername(),
                'support_channel_name' => $channel['name'],
                'support_channel_link' => $channel['link'],
            ]));

            $this->bot->sendTo($chatId, $answer);
        } catch (HttpException $apiException) {
            $this->bot->log(sprintf(
                "Cannot handle info message answer. \nError: %s", $apiException->getMessage()),
                "error",
                ['code' => $apiException->getCode()]
            );
        }
    }
}
