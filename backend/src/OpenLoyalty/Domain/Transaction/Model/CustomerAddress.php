<?php

namespace OpenLoyalty\Domain\Transaction\Model;

use Broadway\Serializer\SerializableInterface;

/**
 * Class CustomerAddress.
 */
class CustomerAddress implements SerializableInterface
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
     * CustomerAddress constructor.
     *
     * @param string $street
     * @param string $address1
     * @param string $province
     * @param string $city
     * @param string $postal
     * @param string $country
     * @param string $address2
     */
    public function __construct($street, $address1, $province, $city, $postal, $country, $address2 = null)
    {
        $this->street = $street;
        $this->address1 = $address1;
        $this->address2 = $address2;
        $this->province = $province;
        $this->city = $city;
        $this->postal = $postal;
        $this->country = $country;
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
            isset($data['address2']) ? $data['address2'] : null
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'street' => $this->street,
            'address1' => $this->address1,
            'address2' => $this->address2,
            'province' => $this->province,
            'city' => $this->city,
            'postal' => $this->postal,
            'country' => $this->country,
        ];
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
}
