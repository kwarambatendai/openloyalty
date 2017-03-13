<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Transaction;

use Assert\Assertion as Assert;
use OpenLoyalty\Domain\Identifier;

/**
 * Class PosId.
 */
class PosId implements Identifier
{
    /**
     * @var string
     */
    protected $posId;

    /**
     * PosId constructor.
     *
     * @param string $posId
     */
    public function __construct($posId)
    {
        Assert::string($posId);
        Assert::uuid($posId);

        $this->posId = $posId;
    }

    public function __toString()
    {
        return $this->posId;
    }
}
