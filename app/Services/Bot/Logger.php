<?php

namespace App\Services\Bot;

use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Log\Logger as BaseLogger;

/**
 * Class Logger
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  13.06.2019
 */
class Logger extends BaseLogger
{
    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    protected function formatMessage($message)
    {
        $date = new DateTime("now", new DateTimeZone("utc"));

        return $date->format('[Y-m-d H:i:s] ') . parent::formatMessage($message);
    }
}
