<?php

namespace App\Services\Bot\Answer;

use App\Models\Church;
use App\Services\Bot\Handlers\CallbackQuery\CancelActions;
use App\Services\Bot\Handlers\CallbackQuery\ConfirmAddressReport;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

/**
 * Class ConfirmAddressReportAnswer
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  08.09.2019
 */
class ConfirmAddressReportAnswer implements AnswerInterface
{
    /**
     * Received address
     *
     * @var string
     */
    private $address;

    /**
     * Related object ID
     *
     * @var int
     */
    private $objectId;

    public function __construct(string $address, int $objectId)
    {
        $this->address = $address;
        $this->objectId = $objectId;
    }

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        /** @var Church $church */
        $church = Church::where('object_id', $this->objectId)->get(['name'])->first();

        return trans(
            "bot.messages.text.confirm_address_request",
            [
                'church' => $church->name,
                "address" => $this->address,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getMarkup()
    {
        return new InlineKeyboardMarkup([
            [[
                "text" => trans("bot.interface.button.confirm_yes"),
                "callback_data" => ConfirmAddressReport::CALLBACK_DATA . "_" . $this->objectId,
            ]], [[
                "text" => trans("bot.interface.button.cancel"),
                "callback_data" => CancelActions::CALLBACK_DATA,
            ]]
        ]);
    }
}
