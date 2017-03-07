<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Customer\Event;

use OpenLoyalty\Domain\Customer\CustomerId;

/**
 * Class CustomerWasRegistered.
 */
class CustomerWasRegistered extends CustomerEvent
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
        if (is_numeric($data['birthDate'])) {
            $tmp = new \DateTime();
            $tmp->setTimestamp($data['birthDate']);
            $data['birthDate'] = $tmp;
        }
        if (is_numeric($data['createdAt'])) {
            $tmp = new \DateTime();
            $tmp->setTimestamp($data['createdAt']);
            $data['createdAt'] = $tmp;
        }
        if (isset($data['updatedAt']) && is_numeric($data['updatedAt'])) {
            $tmp = new \DateTime();
            $tmp->setTimestamp($data['updatedAt']);
            $this->updateAt = $tmp;
        } else {
            $this->updateAt = new \DateTime();
        }

        $this->customerData = $data;
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
        if ($data['createdAt'] instanceof \DateTime) {
            $data['createdAt'] = $data['createdAt']->getTimestamp();
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
        if (is_numeric($data['createdAt'])) {
            $tmp = new \DateTime();
            $tmp->setTimestamp($data['createdAt']);
            $data['createdAt'] = $tmp;
        }

        $event = new self(
            new CustomerId($id),
            $data
        );

        if (isset($data['updatedAt'])) {
            $date = new \DateTime();
            $date->setTimestamp($data['updatedAt']);
            $event->setUpdateAt($date);
        }

        return $event;
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
