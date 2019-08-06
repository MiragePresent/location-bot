<?php
namespace App\Http;

trait MapsTrait
{
    /**
     * Address
     *
     * @return string
     */
    abstract public function getAddress();

    /**
     * Google maps marker Url
     *
     * @return string
     */
    public function getMarkerUrl(): string
    {
        return sprintf(
            "https://maps.google.com/maps?q=%s",
            urlencode($this->getAddress())
        );
    }
}
