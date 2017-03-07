<?php

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
