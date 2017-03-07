<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Level;

use OpenLoyalty\Domain\Identifier;
use Assert\Assertion as Assert;

/**
 * Class SpecialRewardId.
 */
class SpecialRewardId implements Identifier
{
    private $specialRewardId;

    public function __construct($specialRewardId)
    {
        Assert::string($specialRewardId);
        Assert::uuid($specialRewardId);

        $this->specialRewardId = $specialRewardId;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->specialRewardId;
    }
}
