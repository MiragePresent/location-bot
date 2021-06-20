<?php

namespace App\Services\Bot\Answer;

/**
 * Class HelpProjectAnswer
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  20.06.2021
 */
class HelpProjectAnswer implements AnswerInterface
{
    /**
     * @var string
     */
    private $channelName;
    /**
     * @var string
     */
    private $channelLink;

    public function __construct(string $channelName, string $channelLink)
    {
        $this->channelName = $channelName;
        $this->channelLink = $channelLink;
    }

    public function getText(): string
    {
        return trans('bot.messages.text.support_info', [
            'support_channel_name' => $this->channelName,
            'support_channel_link' => $this->channelLink,
        ]);
    }

    public function getMarkup()
    {
        return null;
    }
}
