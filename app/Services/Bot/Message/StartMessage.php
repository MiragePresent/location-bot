<?php

namespace App\Services\Bot\Message;

/**
 * Class StartMessage
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  11.08.2019
 */
class StartMessage implements MessageInterface
{
    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return trans("bot.messages.text.start");
    }

    /**
     * @inheritDoc
     */
    public function getMarkup()
    {
        return null;
    }
}
