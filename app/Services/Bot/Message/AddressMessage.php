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
        return trans("bot.messages.text.church_address", [
            'name' => $this->object->getName(),
            'address' => $this->object->getAddress(),
        ]);
    }

    /**
     * Message buttons
     *
     * @return InlineKeyboardMarkup
     */
    public function getMarkup()
    {
        $buttons[] = [
            'text' => trans('bot.interface.button.show_on_the_map'),
            'url' => $this->object->getMarkerUrl(),
        ];

        if ($this->object->facebook) {
            $buttons[] = [
                'text' => trans('bot.interface.button.facebook'),
                'url' => $this->object->facebook,
            ];
        }

        return new InlineKeyboardMarkup([$buttons]);
    }
}
