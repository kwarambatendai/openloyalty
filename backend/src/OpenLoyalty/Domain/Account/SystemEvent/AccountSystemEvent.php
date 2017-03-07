<?php

namespace OpenLoyalty\Domain\Account\SystemEvent;

use OpenLoyalty\Domain\Account\AccountId;

/**
 * Class AccountSystemEvent.
 */
class AccountSystemEvent
{
    /**
     * @var AccountId
     */
    protected $accountId;

    /**
     * AccountSystemEvent constructor.
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
