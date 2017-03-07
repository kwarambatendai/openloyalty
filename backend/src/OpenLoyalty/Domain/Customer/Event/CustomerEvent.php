<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Customer\Event;

use Broadway\Serializer\SerializableInterface;
use OpenLoyalty\Domain\Customer\CustomerId;

/**
 * Class CustomerEvent.
 */
abstract class CustomerEvent implements SerializableInterface
{
    private $customerId;

    public function __construct(CustomerId $customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return array('customerId' => (string) $this->customerId);
    }
}
