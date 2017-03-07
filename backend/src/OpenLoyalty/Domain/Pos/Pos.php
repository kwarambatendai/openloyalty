<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Pos;

use OpenLoyalty\Domain\Pos\Model\Location;
use Assert\Assertion as Assert;

/**
 * Class Pos.
 */
class Pos
{
    /**
     * @var PosId
     */
    protected $posId;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var Location
     */
    protected $location;

    protected $transactionsAmount = 0;

    protected $transactionsCount = 0;

    public function __construct(PosId $posId, array $data = [])
    {
        $this->posId = $posId;
        $this->setFromArray($data);
    }

    public function setFromArray(array $data = [])
    {
        if (isset($data['name'])) {
            $this->name = $data['name'];
        }
        if (isset($data['description'])) {
            $this->description = $data['description'];
        }
        if (isset($data['identifier'])) {
            $this->identifier = $data['identifier'];
        }
        if (isset($data['location'])) {
            $this->location = Location::deserialize($data['location']);
        }
    }

    public static function validateRequiredData(array $data = [])
    {
        Assert::keyIsset($data, 'name');
        Assert::keyIsset($data, 'identifier');
        Assert::keyIsset($data, 'location');
        Location::validateRequiredData($data['location']);
    }

    /**
     * @return PosId
     */
    public function getPosId()
    {
        return $this->posId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param Location $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return int
     */
    public function getTransactionsAmount()
    {
        return $this->transactionsAmount;
    }

    /**
     * @param int $transactionsAmount
     */
    public function setTransactionsAmount($transactionsAmount)
    {
        $this->transactionsAmount = $transactionsAmount;
    }

    /**
     * @return int
     */
    public function getTransactionsCount()
    {
        return $this->transactionsCount;
    }

    /**
     * @param int $transactionsCount
     */
    public function setTransactionsCount($transactionsCount)
    {
        $this->transactionsCount = $transactionsCount;
    }
}
