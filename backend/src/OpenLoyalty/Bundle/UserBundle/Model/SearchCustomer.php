<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\UserBundle\Model;

use Symfony\Component\Validator\Constraints as ValidationAssert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class SearchCustomer.
 */
class SearchCustomer
{
    /**
     * @var string
     */
    protected $loyaltyCardNumber;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $postcode;

    /**
     * @return string
     */
    public function getLoyaltyCardNumber()
    {
        return $this->loyaltyCardNumber;
    }

    /**
     * @param string $loyaltyCardNumber
     */
    public function setLoyaltyCardNumber($loyaltyCardNumber)
    {
        $this->loyaltyCardNumber = $loyaltyCardNumber;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
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
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * @param string $postcode
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;
    }

    /**
     * @param ExecutionContextInterface $context
     * @ValidationAssert\Callback()
     */
    public function validate(ExecutionContextInterface $context)
    {
        $atLeastOne = false;
        if ($this->loyaltyCardNumber) {
            $atLeastOne = true;
        }
        if ($this->phone) {
            $atLeastOne = true;
        }

        if ($this->email) {
            $atLeastOne = true;
        }

        if ($this->city) {
            $atLeastOne = true;
        }

        if ($this->postcode) {
            $atLeastOne = true;
        }

        if ($this->firstName && $this->lastName) {
            $atLeastOne = true;
        }

        if ($this->firstName && !$this->lastName) {
            $context->buildViolation('This field is required')->atPath('lastName')->addViolation();
        }

        if (!$this->firstName && $this->lastName) {
            $context->buildViolation('This field is required')->atPath('firstName')->addViolation();
        }

        if (!$atLeastOne) {
            $context->buildViolation('Provide at least one field')->addViolation();
        }
    }

    public function toCriteriaArray()
    {
        $criteria = [];

        if ($this->loyaltyCardNumber) {
            $criteria['loyaltyCardNumber'] = $this->loyaltyCardNumber;
        }
        if ($this->phone) {
            $criteria['phone'] = strtolower($this->phone);
        }
        if ($this->email) {
            $criteria['email'] = strtolower($this->email);
        }
        if ($this->city) {
            $criteria['address.city'] = strtolower($this->city);
        }
        if ($this->postcode) {
            $criteria['address.postal'] = strtolower($this->postcode);
        }
        if ($this->lastName) {
            $criteria['firstName'] = strtolower($this->firstName);
            $criteria['lastName'] = strtolower($this->lastName);
        }

        return $criteria;
    }
}
