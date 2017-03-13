<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Seller;

use OpenLoyalty\Domain\Identifier;
use Assert\Assertion as Assert;

/**
 * Class PosId.
 */
class PosId implements Identifier
{
    private $posId;

    /**
     * @param string $posId
     */
    public function __construct($posId)
    {
        Assert::string($posId);
        Assert::uuid($posId);

        $this->posId = $posId;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->posId;
    }
}
