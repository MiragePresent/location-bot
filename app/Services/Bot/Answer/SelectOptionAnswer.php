<?php

namespace App\Services\Bot\Answer;

use TelegramBot\Api\Types\ReplyKeyboardMarkup;

/**
 * Class SelectOptionAnswer
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  11.08.2019
 */
class SelectOptionAnswer implements AnswerInterface
{
    /**
     * Message text
     *
     * @var string
     */
    protected $question;

    /**
     * Message options (keyboard buttons)
     * @var array
     */
    protected $options;

    public function __construct(string $question, array $options)
    {
        $this->question = $question;
        $this->options = $options;
    }

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return $this->question;
    }

    /**
     * @inheritDoc
     */
    public function getMarkup()
    {
        return new ReplyKeyboardMarkup($this->options, true, true);
    }
}
