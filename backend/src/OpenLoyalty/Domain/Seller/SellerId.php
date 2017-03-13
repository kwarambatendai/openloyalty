<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Seller;

use OpenLoyalty\Domain\Identifier;
use Assert\Assertion as Assert;

/**
 * Class SellerId.
 */
class SellerId implements Identifier
{
    private $sellerId;

    /**
     * @param string $sellerId
     */
    public function __construct($sellerId)
    {
        Assert::string($sellerId);
        Assert::uuid($sellerId);

        $this->sellerId = $sellerId;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->sellerId;
    }
}
