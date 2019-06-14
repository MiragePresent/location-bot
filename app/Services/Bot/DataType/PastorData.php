<?php

namespace App\Services\Bot\DataType;

/**
 * Class PastorData
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  14.06.2019
 */
class PastorData extends AbstractDataType
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $phone;

    /**
     * @var string
     */
    public $email;
}
