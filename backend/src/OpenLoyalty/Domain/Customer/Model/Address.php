<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Customer\Model;

use Broadway\Serializer\SerializableInterface;

/**
 * Class Address.
 */
class Address implements SerializableInterface
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

    public static function fromData($addressData)
    {
        $addressData = static::resolveOptions($addressData);
        $address = new self();
        if (isset($addressData['street'])) {
            $address->setStreet($addressData['street']);
        }
        if (isset($addressData['address1'])) {
            $address->setAddress1($addressData['address1']);
        }
        if (isset($addressData['address2'])) {
            $address->setAddress2($addressData['address2']);
        }
        if (isset($addressData['city'])) {
            $address->setCity($addressData['city']);
        }
        if (isset($addressData['country'])) {
            $address->setCountry($addressData['country']);
        }
        if (isset($addressData['postal'])) {
            $address->setPostal($addressData['postal']);
        }
        if (isset($addressData['province'])) {
            $address->setProvince($addressData['province']);
        }

        return $address;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * @param string $address1
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;
    }

    /**
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * @param string $address2
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
    }

    /**
     * @return string
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * @param string $province
     */
    public function setProvince($province)
    {
        $this->province = $province;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getPostal()
    {
        return $this->postal;
    }

    /**
     * @param string $postal
     */
    public function setPostal($postal)
    {
        $this->postal = $postal;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    private static function resolveOptions($data)
    {
        $default = [
            'street' => null,
            'address1' => null,
            'address2' => null,
            'postal' => null,
            'city' => null,
            'province' => null,
            'country' => null,
        ];

        return array_merge($default, $data);
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return static::fromData($data);
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'street' => $this->getStreet(),
            'address1' => $this->getAddress1(),
            'address2' => $this->getAddress2(),
            'postal' => $this->getPostal(),
            'city' => $this->getCity(),
            'province' => $this->getProvince(),
            'country' => $this->getCountry(),
        ];
    }
}
