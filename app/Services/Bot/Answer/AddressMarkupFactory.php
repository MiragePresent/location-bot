<?php

namespace App\Services\Bot\Answer;

use App\Models\Church;
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
     * @param Church $church
     *
     * @return InlineKeyboardMarkup
     */
    public static function create(Church $church)
    {
        $buttons[] = [[
            'text' => trans('bot.interface.button.show_on_the_map'),
            'url' => sprintf(
                "https://maps.google.com/maps?q=%s",
                urlencode($church->address)
            )
        ]];

        if ($church->facebook_url) {
            $buttons[] = [[
                'text' => trans('bot.interface.button.facebook'),
                'url' => $church->facebook_url,
            ]];
        }

//        $buttons[] = [[
//            "text" => trans("bot.interface.button.more"),
//            "callback_data" => MoreFunctions::CALLBACK_DATA . "_" . $object->id,
//        ]];

        return new InlineKeyboardMarkup($buttons);
    }

    /**
     * Google maps marker Url
     *
     * @return string
     */
    public function getMarkerUrl(string $address): string
    {
        return sprintf(
            "https://maps.google.com/maps?q=%s",
            urlencode($address)
        );
    }
}
