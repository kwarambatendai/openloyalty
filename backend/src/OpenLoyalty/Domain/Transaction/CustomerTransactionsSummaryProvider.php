<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Transaction;

interface CustomerTransactionsSummaryProvider
{
    /**
     * @param CustomerId $customerId
     *
     * @return int
     */
    public function getTransactionsCount(CustomerId $customerId);
}
