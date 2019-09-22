<?php

namespace App\Services\Bot\Answer;

use App\Services\Bot\DataType\ObjectData;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

/**
 * Class AddressMessage
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  07.08.2019
 */
class AddressAnswer implements AnswerInterface
{
    /**
     * Object data
     *
     * @var ObjectData
     */
    protected $object;

    /**
     * Distance to the church
     *
     * @var float|null
     */
    protected $distance = null;

    public function __construct(ObjectData $objectData, float $distance = null)
    {
        $this->object = $objectData;
        $this->distance = $distance;
    }

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        $name = $this->object->getName();

        if (!is_null($this->distance) && round($this->distance, 2) > 0 ) {
            $name .= sprintf(" (%01.2f км.)", $this->distance);
        }

        return trans("bot.messages.text.church_address", [
            'name' => $name,
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
        return AddressMarkupFactory::create($this->object);
    }
}
