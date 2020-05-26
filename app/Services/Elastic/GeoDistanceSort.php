<?php

namespace App\Services\Elastic;

use Nord\Lumen\Elasticsearch\Search\Sort\AbstractSort;

/**
 * Class GeoPositionSort
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  26.05.2020
 */
class GeoDistanceSort extends AbstractSort
{
    public const UNIT_KILOMETER = 'km';
    public const UNIT_METER = 'm';
    public const UNIT_MILE = 'mi';

    /**
     * Sorting units
     *
     * @var string
     */
    private $unit;

    /**
     * Field name in document
     *
     * @var string
     */
    private $field;

    /**
     * Location to sort
     *
     * @var array|string
     */
    private $location;

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            '_geo_distance' => [
                'unit' => $this->getUnit(),
                'order' => $this->getOrder(),
                $this->getField() => $this->getLocation(),
            ]
        ];
    }

    /**
     * @return string
     */
    public function getUnit(): string
    {
        return $this->unit;
    }

    /**
     * Set sorting unit
     *
     * @param string $unit
     *
     * @return GeoDistanceSort
     */
    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Set field name for sorting
     *
     * @param string $field
     *
     * @return GeoDistanceSort
     */
    public function setField(string $field): self
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return array<string,float>|array<float,float>|string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param float $latitude
     * @param float $longitude
     *
     * @return GeoDistanceSort
     */
    public function setLocation(float $latitude, float $longitude): self
    {
        $this->location = [
            'lat' => $latitude,
            'lon' => $longitude,
        ];

        return $this;
    }
}
