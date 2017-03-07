<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Campaign;

use OpenLoyalty\Domain\Identifier;
use Assert\Assertion as Assert;

/**
 * Class CustomerId.
 */
class CustomerId implements Identifier
{
    /**
     * @var string
     */
    protected $customerId;

    /**
     * CustomerId constructor.
     *
     * @param string $customerId
     */
    public function __construct($customerId)
    {
        Assert::string($customerId);
        Assert::uuid($customerId);
        $this->customerId = $customerId;
    }

    public function __toString()
    {
        return $this->customerId;
    }

    /**
     * @return string
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }
}
