<?php

namespace App\Services\Bot\Answer;

use App\Services\SdaStorage\DataType\ObjectData;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

/**
 * Class AddressButtonsFactory
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  26.08.2019
 */
class AddressMarkupFactory
{
    /**
     * Creates church address markup
     *
     * @param ObjectData $object
     *
     * @return InlineKeyboardMarkup
     */
    public static function create(ObjectData $object)
    {
        $buttons[] = [[
            'text' => trans('bot.interface.button.show_on_the_map'),
            'url' => $object->getMarkerUrl(),
        ]];

        if ($object->facebook) {
            $buttons[] = [[
                'text' => trans('bot.interface.button.facebook'),
                'url' => $object->facebook,
            ]];
        }

//        $buttons[] = [[
//            "text" => trans("bot.interface.button.more"),
//            "callback_data" => MoreFunctions::CALLBACK_DATA . "_" . $object->id,
//        ]];

        return new InlineKeyboardMarkup($buttons);
    }
}
