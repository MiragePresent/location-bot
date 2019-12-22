<?php

namespace App\Services\Bot\Exception;

use Exception;
use TelegramBot\Api\Types\Update;
use Throwable;

/**
 * Class UpdateParseException
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  22.12.2019
 */
class UpdateParseException extends Exception
{
    /**
     * @var Update
     */
    private $update;

    public function __construct($message = "", Update $update = null, Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->update = $update;
    }

    /**
     * @return Update
     */
    public function getUpdate(): Update
    {
        return $this->update;
    }
}
