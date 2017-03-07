<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Account\Command;

use OpenLoyalty\Domain\Account\AccountId;

/**
 * Class AccountCommand.
 */
abstract class AccountCommand
{
    /**
     * @var AccountId
     */
    protected $accountId;

    /**
     * AccountCommand constructor.
     *
     * @param AccountId $accountId
     */
    public function __construct(AccountId $accountId)
    {
        $this->accountId = $accountId;
    }

    /**
     * @return AccountId
     */
    public function getAccountId()
    {
        return $this->accountId;
    }
}
