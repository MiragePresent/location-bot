<?php

use App\Services\SdaStorage\DataType;

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
        $dataType = new DataType\CoordinatesData();
        $dataType->loadFrom([
            'latitude' => 50.724902,
            'longitude' => 29.809823,
        ]);

        static::assertNotEmpty($dataType->latitude);
        static::assertNotEmpty($dataType->longitude);
    }

    public function test_loadFrom_with_nested_objects()
    {
        $dataType =new DataType\LocalityData();
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
        static::assertInstanceOf(DataType\CoordinatesData::class, $dataType->coordinates);
        static::assertNotEmpty($dataType->coordinates->latitude);
        static::assertNotEmpty($dataType->coordinates->longitude);
    }

    public function test_loadFrom_with_empty_nested_object()
    {
        $dataType =new DataType\LocalityData();
        $dataType->loadFrom([
            'id' => 4234,
            'name' => 'Some locality',
            'region' => 'Some region',
            'coordinates' => []
        ]);

        static::assertNotEmpty($dataType->id);
        static::assertNotEmpty($dataType->name);
        static::assertNotEmpty($dataType->region);
        static::assertEmpty($dataType->coordinates);
    }
}
