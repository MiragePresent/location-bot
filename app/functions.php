<?php
/**
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  13.06.2019
 */

/**
 * Converts UTF-8 bytes
 *
 * @param string $bytes
 *
 * @return string
 *
 * @link https://stackoverflow.com/questions/31430587/how-to-send-emoji-with-telegram-bot-api
 */
function emoji(string $bytes): string
{
    $pattern = '@\\\x([0-9a-fA-F]{2})@x';

    return preg_replace_callback(
        $pattern,
        function ($captures) {
            return chr(hexdec($captures[1]));
        },
        $bytes
    );
}
