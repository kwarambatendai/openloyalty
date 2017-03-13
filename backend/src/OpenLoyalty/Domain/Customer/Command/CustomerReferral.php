<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Customer\Command;

use OpenLoyalty\Domain\Customer\CustomerId;

/**
 * Class CustomerReferral.
 */
class CustomerReferral extends CustomerCommand
{
    /** @var CustomerId */
    protected $referralCustomerId;

    /**
     * CustomerReferral constructor.
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
    public function getReferralCustomerId(): CustomerId
    {
        return $this->referralCustomerId;
    }
}
