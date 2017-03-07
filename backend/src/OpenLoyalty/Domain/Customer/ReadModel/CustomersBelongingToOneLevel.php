<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Customer\ReadModel;

use Broadway\ReadModel\ReadModelInterface;
use Broadway\Serializer\SerializableInterface;
use OpenLoyalty\Domain\Customer\LevelId;

/**
 * Class CustomersBelongingToOneLevel.
 */
class CustomersBelongingToOneLevel implements ReadModelInterface, SerializableInterface
{
    /**
     * @var LevelId
     */
    protected $levelId;

    /**
     * @var array
     */
    protected $customers = [];

    /**
     * CustomersBelongingToOneLevel constructor.
     *
     * @param LevelId $levelId
     */
    public function __construct(LevelId $levelId)
    {
        $this->levelId = $levelId;
    }

    public function addCustomer(CustomerDetails $customer)
    {
        $this->customers[] = [
            'customerId' => $customer->getCustomerId()->__toString(),
            'firstName' => $customer->getFirstName(),
            'lastName' => $customer->getLastName(),
            'email' => $customer->getEmail(),
        ];
    }

    public function removeCustomer(CustomerDetails $customer)
    {
        foreach ($this->customers as $key => $cust) {
            if ($cust['customerId'] == $customer->getCustomerId()->__toString()) {
                unset($this->customers[$key]);
                break;
            }
        }
    }

    /**
     * @return array
     */
    public function getCustomers()
    {
        return $this->customers;
    }

    /**
     * @return LevelId
     */
    public function getLevelId()
    {
        return $this->levelId;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->levelId->__toString();
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        $obj = new self(new LevelId($data['levelId']));
        $obj->customers = $data['customers'];

        return $obj;
    }

    /**
     * @return array
     */
    public function serialize()
    {
        $customers = array_values($this->customers);

        return [
            'levelId' => $this->getId(),
            'customers' => $customers,
        ];
    }
}
