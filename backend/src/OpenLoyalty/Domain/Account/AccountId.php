<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Account;

use OpenLoyalty\Domain\Identifier;
use Assert\Assertion as Assert;

/**
 * Class AccountId.
 */
class AccountId implements Identifier
{
    private $accountId;

    /**
     * @param string $accountId
     */
    public function __construct($accountId)
    {
        Assert::string($accountId);
        Assert::uuid($accountId);

        $this->accountId = $accountId;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->accountId;
    }
}
