<?php

namespace App\Services\Bot\Answer;

use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

/**
 * Simple text message
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  11.08.2019
 */
class TextAnswer implements AnswerInterface
{
    /**
     * Message text
     *
     * @var string
     */
    protected $text;

    /**
     * @var InlineKeyboardMarkup
     */
    private $markup;

    /**
     * TextMessage constructor.
     *
     * @param string $text
     * @param InlineKeyboardMarkup $markup Optional keyboard
     */
    public function __construct(string $text, InlineKeyboardMarkup $markup = null)
    {
        $this->text = $text;
        $this->markup = $markup;
    }

    /**
     * Sets message text
     *
     * @param string $text
     *
     * @return TextAnswer
     */
    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @inheritDoc
     */
    public function getMarkup()
    {
        return $this->markup;
    }
}
