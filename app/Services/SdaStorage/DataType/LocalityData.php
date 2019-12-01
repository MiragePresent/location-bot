<?php

namespace App\Services\SdaStorage\DataType;

/**
 * Class LocalityData
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  14.06.2019
 */
class LocalityData extends AbstractDataType
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
    public $region;

    /**
     * @var string
     */
    public $nameUk;

    /**
     * @var string
     */
    public $nameRu;

    /**
     * @var string
     */
    public $nameEn;

    /**
     * Locality address
     *
     * @var string
     */
    public $techName;

    /**
     * @var string
     */
    public $regionUk;

    /**
     * @var string
     */
    public $regionRu;

    /**
     * @var string
     */
    public $regionEn;

    /**
     * @var string
     */
    public $placeId;

    /**
     * @var bool
     */
    public $decoded;

    /**
     * @var CoordinatesData
     */
    public $coordinates;

    protected $dataType = [
        'coordinates' => CoordinatesData::class,
    ];

    protected $aliases = [
        'name_uk' => 'nameUk',
        'name_ru' => 'nameRu',
        'name_en' => 'nameEn',
        'tech_name' => 'techName',
        'region_uk' => 'regionUk',
        'region_ru' => 'regionRu',
        'region_en' => 'regionEn',
        'place_id' => 'placeId',
    ];

    /**
     * Returns default name
     *
     * @return string
     */
    public function getName(): string
    {
        $name = $this->name;

        if (!$name) {
            $name = $this->nameUk;
        }

        if (!$name) {
            $name = $this->nameRu;
        }

        if (!$name) {
            $name = $this->nameEn;
        }

        return $name ?: 'Церква АСД';
    }

    /**
     * Returns default region
     *
     * @return string
     */
    public function getRegion(): string
    {
        $region = $this->region;

        if (!$region) {
            $region = $this->regionUk;
        }

        if (!$region) {
            $region = $this->regionRu;
        }

        if (!$region) {
            $region = $this->regionEn;
        }

        return $region ?: 'Україна';
    }
}
