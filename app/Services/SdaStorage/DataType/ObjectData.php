<?php

namespace App\Services\SdaStorage\DataType;

use App\Http\MapsTrait;

/**
 * Class ObjectData
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  14.06.2019
 *
 * @method ObjectData loadFrom(array $data)
 */
class ObjectData extends AbstractDataType
{
    use MapsTrait;

    /**
     * Cache file time
     *
     * @var int
     */
    public const CACHE_LIFE_TIME = 14 * 24 * 60 * 60;

    /**
     * Object type church
     *
     * @var string
     */
    public const TYPE_CHURCH = 'church';

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $number;

    /**
     * @var string
     */
    public $code;

    /**
     * @var string
     */
    public $type;

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
    public $address;

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
    public $addressUk;

    /**
     * @var string
     */
    public $addressRu;

    /**
     * @var string
     */
    public $addressEn;

    /**
     * @var bool
     */
    public $decoded;

    /**
     * @var string
     */
    public $calendar;

    /**
     * @var string
     */
    public $facebook;

    /**
     * @var CoordinatesData
     */
    public $coordinates;

    /**
     * @var PastorData
     */
    public $pastor;

    /**
     * @var LocalityData
     */
    public $locality;

    /**
     * @var null|PhotoData
     */
    public $photo;

    protected $dataType = [
        'coordinates'   => CoordinatesData::class,
        'photo'         => PhotoData::class,
        'pastor'        => PastorData::class,
        'locality'      => LocalityData::class,
    ];

    protected $aliases = [
        'name_uk' => 'nameUk',
        'name_ru' => 'nameRu',
        'name_en' => 'nameEn',
        'tech_name' => 'techName',
        'region_uk' => 'regionUk',
        'region_ru' => 'regionRu',
        'region_en' => 'regionEn',
        'address_uk' => 'addressUk',
        'address_ru' => 'addressRu',
        'address_en' => 'addressEn',
    ];

    public function getId(): int
    {
        return $this->id;
    }

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

    /**
     * Returns default value of address
     *
     * @return string
     */
    public function getAddress(): string
    {
        $address = $this->address;

        if (!$address) {
            $address = $this->addressUk;
        }

        if (!$address) {
            $address = $this->addressRu;
        }

        if (!$address) {
            $address = $this->addressEn;
        }

        return $address ?: 'Україна';
    }
}
