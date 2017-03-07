<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Customer\SystemEvent;

use OpenLoyalty\Domain\Customer\CustomerId;

/**
 * Class CustomerSystemEvent.
 */
class CustomerSystemEvent
{
    /**
     * @var CustomerId
     */
    protected $customerId;

    /**
     * CustomerSystemEvent constructor.
     *
     * @param CustomerId $customerId
     */
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
}
