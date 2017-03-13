<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyaltyPlugin\SalesManagoBundle\Model;

/**
 * Contact.
 *
 * @category    DivanteOpenLoyalty
 *
 * @author      Michal Kajszczak <mkajszczak@divante.pl>
 * @copyright   Copyright (C) 2016 Divante Sp. z o.o.
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class Contact
{
    /**
     * @var string
     */
    protected $company;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var int
     */
    protected $phone;

    /**
     * @var string
     */
    protected $streetAddress;

    /**
     * @var string
     */
    protected $zipCode;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var array
     */
    protected $properties;

    /**
     * @var array
     */
    protected $tags;

    /**
     * @var false|string
     */
    protected $birthday;

    /**
     * @var array
     */
    protected $details;

    /**
     * Contact constructor.
     *
     * @param array $data
     */
    public function __construct($data)
    {
        $this->company = $data['company'];
        $this->name = $data['firstName'].' '.$data['lastName'];
        $this->email = $data['email'];
        $this->phone = $data['phone'];
        $this->streetAddress = $data['address']['street'].' '.$data['address']['address1'];
        $this->zipCode = $data['address']['postal'];
        $this->country = $data['address']['country'];
        $this->properties = $this->createProperities($data);
        $this->birthday = date('Ymd', $data['birthDate']);
        $this->city = $data['address']['city'];
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getStreetAddress()
    {
        return $this->streetAddress;
    }

    /**
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return mixed
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return array
     */
    public function toSalesManagoArray()
    {
        $array = [
            'contact' => [
                'company' => $this->getCompany(),
                'email' => $this->getEmail(),
                'name' => $this->getName(),
                'phone' => $this->getPhone(),
                'address' => [
                    'streetAddress' => $this->getStreetAddress(),
                    'zipCode' => $this->getZipCode(),
                    'city' => $this->getCity(),
                    'country' => $this->getCountry(),
                ],
            ],
            'birthday' => $this->getBirthday(),
        ];
        if (!empty($this->getProperties())) {
            $array['properties'] = $this->getProperties();
        }

        return $array;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function createProperities($data)
    {
        $properties = [
            'agreement1' => $data['agreement1'],
            'agreement2' => $data['agreement2'],
            'levelId' => ($data['levelId']) ? $data['levelId'] : null,
            'registration_date' => date('Y-m-d', $data['createdAt']),
            'clv' => isset($data['clv']) ? $data['clv'] : null,
            'transactionsAmountToNextLevel' => isset($data['transactionsAmountToNextLevel']) ? $data['transactionsAmountToNextLevel'] : null,
            'avo' => isset($data['avo']) ? $data['avo'] : null,
            'levelDiscount' => isset($data['levelDiscount']) ? $data['levelDiscount'] : null,
            'customer_orders' => isset($data['customer_orders']) ? $data['customer_orders'] : null,
        ];

        return $properties;
    }
}
