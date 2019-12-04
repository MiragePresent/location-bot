<?php

namespace App\Services\Bot\Answer;

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
     * TextMessage constructor.
     *
     * @param string $text
     */
    public function __construct(string $text)
    {
        $this->text = $text;
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
        return null;
    }
}
