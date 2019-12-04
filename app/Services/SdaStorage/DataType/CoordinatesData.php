<?php

namespace App\Services\SdaStorage\DataType;

/**
 * Class CoordinatesData
 *
 * location_bob_9087
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  14.06.2019
 */
class CoordinatesData extends AbstractDataType
{
    protected $aliases = [
        'lat' => 'latitude',
        'lng' => 'longitude',
    ];

    /**
     * Latitude
     *
     * @var float
     */
    public $latitude;

    /**
     * Longitude
     *
     * @var float
     */
    public $longitude;
}
