<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Transaction\Command;

use OpenLoyalty\Domain\Transaction\CustomerId;
use OpenLoyalty\Domain\Transaction\TransactionId;

/**
 * Class AssignCustomerToTransaction.
 */
class AssignCustomerToTransaction extends TransactionCommand
{
    /**
     * @var CustomerId
     */
    protected $customerId;

    public function __construct(TransactionId $transactionId, CustomerId $customerId)
    {
        parent::__construct($transactionId);
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
