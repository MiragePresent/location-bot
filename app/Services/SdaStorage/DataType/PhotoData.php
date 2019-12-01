<?php

namespace App\Services\SdaStorage\DataType;

/**
 * Class PhotoData
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  14.06.2019
 */
class PhotoData extends AbstractDataType
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $size;

    /**
     * @var int
     */
    public $width;

    /**
     * @var int
     */
    public $height;

    /**
     * @var string
     */
    public $originalName;
}
