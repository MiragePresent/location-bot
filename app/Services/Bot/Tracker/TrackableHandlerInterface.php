<?php

namespace App\Services\Bot\Tracker;

/**
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  11.04.2022
 */
interface TrackableHandlerInterface
{
    public static function handlerType(): string;
}
