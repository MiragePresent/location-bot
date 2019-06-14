<?php

namespace App\Services\Bot\DataType;

/**
 * Class AbstractDataType
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  14.06.2019
 */
abstract class AbstractDataType
{
    /**
     * Fields map
     *
     * @var array
     */
    protected $dataType = [];

    /**
     * Aliases map
     *
     * @var array
     */
    protected $aliases = [];

    /**
     * Load data from array
     *
     * @param array $data
     *
     * @return AbstractDataType
     */
    public function loadFrom(array $data): AbstractDataType
    {
        foreach ($data as $key => $value) {
            $propName = $this->aliases[$key] ?? $key;

            if (isset($this->dataType[$key]) && !empty($value)) {
                /** @var AbstractDataType $dataType */
                $dataType = new $this->dataType[$key]();
                $this->{$propName} = $dataType->loadFrom($value);
            } else {
                $this->{$propName} = $value;
            }
        }

        return $this;
    }
}
