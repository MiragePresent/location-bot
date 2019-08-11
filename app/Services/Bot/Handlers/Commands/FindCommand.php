<?php

namespace App\Services\Bot\Handlers\Commands;

use App\Services\Bot\Handlers\AbstractCommandHandler;
use App\Services\Bot\Answer\FindCommandAnswer;
use TelegramBot\Api\Types\Message;

/**
 * Class FindCommandInterface
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  13.06.2019
 */
class FindCommand extends AbstractCommandHandler
{
    /**
     * Command signature
     *
     * @var string
     */
    public const COMMAND_SIGNATURE = 'find';

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
            "Running find command by: %s",
            $message->getFrom()->toJson()
        ));

        $answer = new FindCommandAnswer();

        $this->bot->sendTo($message->getChat()->getId(), $answer);
    }
}
