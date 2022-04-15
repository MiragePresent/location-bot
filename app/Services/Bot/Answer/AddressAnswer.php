<?php

namespace App\Services\Bot\Answer;

use App\Models\Church;
use App\Services\SdaStorage\DataType\ObjectData;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

/**
 * Class AddressMessage
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  07.08.2019
 */
class AddressAnswer implements AnswerInterface
{
    public function __construct(protected Church $church, protected ?ObjectData $object = null) {}

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return trans("bot.messages.text.church_address", [
            'name' => $this->church->name,
            'address' => $this->church->address,
        ]);
    }

    /**
     * Message buttons
     *
     * @return InlineKeyboardMarkup
     */
    public function getMarkup()
    {
        return AddressMarkupFactory::create($this->church, $this->object);
    }
}
