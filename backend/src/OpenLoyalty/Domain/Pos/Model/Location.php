<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Pos\Model;

use Broadway\Serializer\SerializableInterface;
use Assert\Assertion as Assert;

/**
 * Class Location.
 */
class Location implements SerializableInterface
{
    /**
     * @var string
     */
    protected $street;

    /**
     * @var string
     */
    protected $address1;

    /**
     * @var string
     */
    protected $address2;

    /**
     * @var string
     */
    protected $province;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $postal;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var GeoPoint
     */
    protected $geoPoint;

    /**
     * Location constructor.
     *
     * @param string $street
     * @param string $address1
     * @param string $province
     * @param string $city
     * @param string $postal
     * @param string $country
     * @param string $address2
     * @param null   $lat
     * @param null   $long
     * @param bool   $disableValidation
     */
    public function __construct($street, $address1, $province, $city, $postal, $country, $address2 = null, $lat = null, $long = null, $disableValidation = false)
    {
        if (!$disableValidation) {
            Assert::notBlank($street);
            Assert::notBlank($address1);
            Assert::notBlank($province);
            Assert::notBlank($city);
            Assert::notBlank($postal);
            Assert::notBlank($country);
        }

        $this->street = $street;
        $this->address1 = $address1;
        $this->province = $province;
        $this->city = $city;
        $this->postal = $postal;
        $this->country = $country;
        $this->address2 = $address2;
        if ($lat && $long) {
            $this->geoPoint = new GeoPoint($lat, $long);
        }
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            isset($data['street']) ? $data['street'] : null,
            isset($data['address1']) ? $data['address1'] : null,
            isset($data['province']) ? $data['province'] : null,
            isset($data['city']) ? $data['city'] : null,
            isset($data['postal']) ? $data['postal'] : null,
            isset($data['country']) ? $data['country'] : null,
            isset($data['address2']) ? $data['address2'] : null,
            isset($data['lat']) ? $data['lat'] : null,
            isset($data['long']) ? $data['long'] : null
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        $data = [
            'street' => $this->street,
            'address1' => $this->address1,
            'address2' => $this->address2,
            'postal' => $this->postal,
            'city' => $this->city,
            'province' => $this->province,
            'country' => $this->country,
        ];

        if ($this->geoPoint) {
            $data = array_merge($data, $this->geoPoint->serialize());
        }

        return $data;
    }

    public static function validateRequiredData(array $data = [])
    {
        Assert::keyIsset($data, 'street');
        Assert::keyIsset($data, 'address1');
        Assert::keyIsset($data, 'province');
        Assert::keyIsset($data, 'city');
        Assert::keyIsset($data, 'postal');
        Assert::keyIsset($data, 'country');
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @return string
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * @return string
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getPostal()
    {
        return $this->postal;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return GeoPoint
     */
    public function getGeoPoint()
    {
        return $this->geoPoint;
    }
}
