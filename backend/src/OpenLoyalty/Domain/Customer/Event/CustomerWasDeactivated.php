<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Customer\Event;

use OpenLoyalty\Domain\Customer\CustomerId;

/**
 * Class CustomerWasDeactivated.
 */
class CustomerWasDeactivated extends CustomerEvent
{
    /**
     * @var \DateTime
     */
    protected $deactivatedAt;

    public function __construct(CustomerId $customerId)
    {
        parent::__construct($customerId);
        $this->deactivatedAt = new \DateTime();
    }

    public function serialize()
    {
        return array_merge(parent::serialize(), array(
            'deactivatedAt' => $this->deactivatedAt ? $this->deactivatedAt->getTimestamp() : null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public static function deserialize(array $data)
    {
        $id = $data['customerId'];
        $customer = new self(
            new CustomerId($id)
        );

        if (isset($data['deactivatedAt'])) {
            $date = new \DateTime();
            $date->setTimestamp($data['deactivatedAt']);
            $customer->setDeactivatedAt($date);
        }

        return $customer;
    }

    /**
     * @return \DateTime
     */
    public function getDeactivatedAt()
    {
        return $this->deactivatedAt;
    }

    /**
     * @param \DateTime $deactivatedAt
     */
    public function setDeactivatedAt($deactivatedAt)
    {
        $this->deactivatedAt = $deactivatedAt;
    }
}
