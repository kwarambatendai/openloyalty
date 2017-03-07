<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Account\SystemEvent;

use OpenLoyalty\Domain\Account\AccountId;
use OpenLoyalty\Domain\Account\CustomerId;

/**
 * Class AccountCreatedSystemEvent.
 */
class AccountCreatedSystemEvent
{
    /**
     * @var AccountId
     */
    protected $accountId;

    /**
     * @var CustomerId
     */
    protected $customerId;

    /**
     * AccountCreatedSystemEvent constructor.
     *
     * @param AccountId  $accountId
     * @param CustomerId $customerId
     */
    public function __construct(AccountId $accountId, CustomerId $customerId = null)
    {
        $this->customerId = $customerId;
        $this->accountId = $accountId;
    }

    /**
     * @return AccountId
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }
}
