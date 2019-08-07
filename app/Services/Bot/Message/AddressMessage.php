<?php

namespace App\Services\Bot\Message;

use App\Services\Bot\DataType\ObjectData;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

/**
 * Class AddressMessage
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  07.08.2019
 */
class AddressMessage implements MessageInterface
{
    /**
     * Object data
     *
     * @var ObjectData
     */
    protected $object;

    public function __construct(ObjectData $objectData)
    {
        $this->object = $objectData;
    }

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        // ⛪
        $church = emoji("\xE2\x9B\xAA");

        return "*{$this->object->getName()}* {$church}\n{$this->object->getAddress()}";
    }

    /**
     * Message buttons
     *
     * @return InlineKeyboardMarkup
     */
    public function getMarkup()
    {
        $buttons[] = [ 'text' => "Відкрити на карті", 'url' => $this->object->getMarkerUrl() ];

        if ($this->object->facebook) {
            $buttons[] = ['text' => 'Facebook', 'url' => $this->object->facebook];
        }

        return new InlineKeyboardMarkup([$buttons]);
    }
}
