<?php

use App\Services\Bot\DataType\CoordinatesData;
use App\Services\Bot\DataType\LocalityData;

/**
 * Class DataTypeTest
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  14.06.2019
 */
class DataTypeTest extends TestCase
{
    public function test_loadFrom_method()
    {
        $dataType = new CoordinatesData();
        $dataType->loadFrom([
            'latitude' => 50.724902,
            'longitude' => 29.809823,
        ]);

        static::assertNotEmpty($dataType->latitude);
        static::assertNotEmpty($dataType->longitude);
    }

    public function test_loadFrom_with_nested_objects()
    {
        $dataType =new LocalityData();
        $dataType->loadFrom([
            'id' => 4234,
            'name' => 'Some locality',
            'region' => 'Some region',
            'coordinates' => [
                'latitude' => 50.724902,
                'longitude' => 29.809823,
            ]
        ]);

        static::assertNotEmpty($dataType->id);
        static::assertNotEmpty($dataType->name);
        static::assertNotEmpty($dataType->region);
        static::assertInstanceOf(CoordinatesData::class, $dataType->coordinates);
        static::assertNotEmpty($dataType->coordinates->latitude);
        static::assertNotEmpty($dataType->coordinates->longitude);
    }

    public function test_loadFrom_with_empty_nested_object()
    {
        $dataType =new LocalityData();
        $dataType->loadFrom([
            'id' => 4234,
            'name' => 'Some locality',
            'region' => 'Some region',
            'coordinates' => [
                'latitude' => 50.724902,
                'longitude' => 29.809823,
            ]
        ]);

        static::assertNotEmpty($dataType->id);
        static::assertNotEmpty($dataType->name);
        static::assertNotEmpty($dataType->region);
        static::assertNull($dataType->coordinates);
    }
}
