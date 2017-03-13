<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Pos\Model;

use Broadway\Serializer\SerializableInterface;

/**
 * Class GeoPoint.
 */
class GeoPoint implements SerializableInterface
{
    /**
     * @var string
     */
    protected $lat;

    /**
     * @var string
     */
    protected $long;

    /**
     * GeoPoint constructor.
     *
     * @param string $lat
     * @param string $long
     */
    public function __construct($lat, $long)
    {
        $this->lat = $lat;
        $this->long = $long;
    }

    /**
     * @return string
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @return string
     */
    public function getLong()
    {
        return $this->long;
    }

    public static function deserialize(array $data)
    {
        return new self($data['lat'], $data['long']);
    }

    public function serialize()
    {
        return [
            'lat' => $this->lat,
            'long' => $this->long,
        ];
    }
}
