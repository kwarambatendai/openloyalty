<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Customer\Command;

use OpenLoyalty\Domain\Customer\CustomerId;

/**
 * Class CustomerCommand.
 */
abstract class CustomerCommand
{
    protected $customerId;

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
