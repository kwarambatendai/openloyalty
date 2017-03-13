<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Account\Command;

use OpenLoyalty\Domain\Account\AccountId;
use OpenLoyalty\Domain\Account\CustomerId;

/**
 * Class CreateAccount.
 */
class CreateAccount extends AccountCommand
{
    /**
     * @var CustomerId
     */
    protected $customerId;

    /**
     * CreateAccount constructor.
     *
     * @param AccountId  $accountId
     * @param CustomerId $customerId
     */
    public function __construct(AccountId $accountId, CustomerId $customerId)
    {
        parent::__construct($accountId);
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
