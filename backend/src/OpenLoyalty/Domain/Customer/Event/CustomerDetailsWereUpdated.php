<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Customer\Event;

use OpenLoyalty\Domain\Customer\CustomerId;

/**
 * Class CustomerDetailsWereUpdated.
 */
class CustomerDetailsWereUpdated extends CustomerEvent
{
    private $customerData;

    /**
     * @var \DateTime
     */
    protected $updateAt;

    public function __construct(CustomerId $customerId, array $customerData)
    {
        parent::__construct($customerId);

        $data = $customerData;
        if (isset($data['birthDate']) && is_numeric($data['birthDate'])) {
            $tmp = new \DateTime();
            $tmp->setTimestamp($data['birthDate']);
            $data['birthDate'] = $tmp;
        }
        if (isset($data['createdAt']) && is_numeric($data['createdAt'])) {
            $tmp = new \DateTime();
            $tmp->setTimestamp($data['createdAt']);
            $data['createdAt'] = $tmp;
        }
        $this->customerData = $data;
        $this->updateAt = new \DateTime();
    }

    /**
     * @return array
     */
    public function getCustomerData()
    {
        return $this->customerData;
    }

    public function serialize()
    {
        $data = $this->customerData;
        if ($data['birthDate'] instanceof \DateTime) {
            $data['birthDate'] = $data['birthDate']->getTimestamp();
        }

        return array_merge(parent::serialize(), array(
            'customerData' => $data,
            'updatedAt' => $this->updateAt ? $this->updateAt->getTimestamp() : null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public static function deserialize(array $data)
    {
        $id = $data['customerId'];
        $data = $data['customerData'];
        if (is_numeric($data['birthDate'])) {
            $tmp = new \DateTime();
            $tmp->setTimestamp($data['birthDate']);
            $data['birthDate'] = $tmp;
        }

        $customer = new self(
            new CustomerId($id),
            $data
        );

        if (isset($data['updatedAt'])) {
            $date = new \DateTime();
            $date->setTimestamp($data['updatedAt']);
            $customer->setUpdateAt($date);
        }

        return $customer;
    }

    /**
     * @return \DateTime
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }

    /**
     * @param \DateTime $updateAt
     */
    public function setUpdateAt($updateAt)
    {
        $this->updateAt = $updateAt;
    }
}
