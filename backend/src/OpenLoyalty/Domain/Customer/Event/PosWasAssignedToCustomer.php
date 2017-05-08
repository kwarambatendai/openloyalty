<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Customer\Event;

use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\PosId;

/**
 * Class PosWasAssignedToCustomer.
 */
class PosWasAssignedToCustomer extends CustomerEvent
{
    /**
     * @var PosId
     */
    protected $posId;

    /**
     * @var \DateTime
     */
    protected $updateAt;

    public function __construct(CustomerId $customerId, PosId $posId)
    {
        parent::__construct($customerId);
        $this->posId = $posId;
        $this->updateAt = new \DateTime();
    }

    public function serialize()
    {
        return array_merge(parent::serialize(), [
            'posId' => $this->posId->__toString(),
            'updatedAt' => $this->updateAt ? $this->updateAt->getTimestamp() : null,
        ]);
    }

    /**
     * @param array $data
     *
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        $event = new self(new CustomerId($data['customerId']), new PosId($data['posId']));
        if (isset($data['updatedAt'])) {
            $date = new \DateTime();
            $date->setTimestamp($data['updatedAt']);
            $event->setUpdateAt($date);
        }

        return $event;
    }

    /**
     * @return PosId
     */
    public function getPosId()
    {
        return $this->posId;
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
