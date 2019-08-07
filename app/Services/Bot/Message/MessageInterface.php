<?php

namespace App\Services\Bot\Message;

/**
 * Class MessageInterface
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  07.08.2019
 */
interface MessageInterface
{
    /**
     * Message text
     *
     * @return string
     */
    public function getText(): string;

    /**
     * Message markup
     *
     * @return mixed
     */
    public function getMarkup();
}
