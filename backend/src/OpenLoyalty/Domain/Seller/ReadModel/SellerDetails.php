<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Seller\ReadModel;

use Broadway\ReadModel\ReadModelInterface;
use Broadway\Serializer\SerializableInterface;
use OpenLoyalty\Domain\Seller\PosId;
use OpenLoyalty\Domain\Seller\SellerId;

/**
 * Class SellerDetails.
 */
class SellerDetails implements ReadModelInterface, SerializableInterface
{
    /**
     * @var SellerId
     */
    protected $sellerId;

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
    protected $email;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var PosId
     */
    protected $posId;

    /**
     * @var string
     */
    protected $posName;

    /**
     * @var string
     */
    protected $posCity;

    protected $active = false;

    protected $deleted = false;

    /**
     * SellerDetails constructor.
     *
     * @param SellerId $sellerId
     */
    public function __construct(SellerId $sellerId)
    {
        $this->sellerId = $sellerId;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        $seller = new self(new SellerId($data['sellerId']));
        $seller->firstName = $data['firstName'];
        $seller->lastName = $data['lastName'];
        $seller->email = $data['email'];
        $seller->phone = $data['phone'];
        $seller->posId = new PosId($data['posId']);
        if (isset($data['posName'])) {
            $seller->posName = $data['posName'];
        }
        if (isset($data['posCity'])) {
            $seller->posCity = $data['posCity'];
        }
        if (isset($data['active'])) {
            $seller->active = $data['active'];
        }
        if (isset($data['deleted'])) {
            $seller->deleted = $data['deleted'];
        }

        return $seller;
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'email' => $this->getEmail(),
            'phone' => $this->getPhone(),
            'posId' => $this->getPosId()->__toString(),
            'sellerId' => $this->sellerId->__toString(),
            'active' => $this->active,
            'deleted' => $this->deleted,
            'posName' => $this->posName,
            'posCity' => $this->posCity,
        ];
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->getSellerId()->__toString();
    }

    /**
     * @return SellerId
     */
    public function getSellerId()
    {
        return $this->sellerId;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
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
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return PosId
     */
    public function getPosId()
    {
        return $this->posId;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @param PosId $posId
     */
    public function setPosId($posId)
    {
        $this->posId = $posId;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return string
     */
    public function getPosName()
    {
        return $this->posName;
    }

    /**
     * @param string $posName
     */
    public function setPosName($posName)
    {
        $this->posName = $posName;
    }

    /**
     * @return string
     */
    public function getPosCity()
    {
        return $this->posCity;
    }

    /**
     * @param string $posCity
     */
    public function setPosCity($posCity)
    {
        $this->posCity = $posCity;
    }
}
