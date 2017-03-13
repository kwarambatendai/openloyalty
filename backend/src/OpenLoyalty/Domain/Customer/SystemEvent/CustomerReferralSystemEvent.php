<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Customer\SystemEvent;

use OpenLoyalty\Domain\Customer\CustomerId;

class CustomerReferralSystemEvent extends CustomerSystemEvent
{
    /** @var CustomerId */
    protected $referralCustomerId;

    /**
     * CustomerReferralSystemEvent constructor.
     *
     * @param CustomerId $customerId
     * @param CustomerId $referralCustomerId
     */
    public function __construct(CustomerId $customerId, CustomerId $referralCustomerId)
    {
        $this->referralCustomerId = $referralCustomerId;

        parent::__construct($customerId);
    }

    /**
     * @return CustomerId
     */
    public function getReferralCustomerId()
    {
        return $this->referralCustomerId;
    }
}
