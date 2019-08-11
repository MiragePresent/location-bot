<?php

namespace App\Services\Bot\Message;

/**
 * Class HelpMessage
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  11.08.2019
 */
class HelpMessage implements MessageInterface
{
    /**
     * Bot username for help message
     *
     * @var string
     */
    protected $botUsername;

    /**
     * HelpMessage constructor.
     *
     * @param string $botUsername
     */
    public function __construct(string $botUsername)
    {
        $this->botUsername = $botUsername;
    }

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return trans("bot.messages.text.help", ["bot_username" => $this->botUsername]);
    }

    /**
     * @inheritDoc
     */
    public function getMarkup()
    {
        return null;
    }
}
