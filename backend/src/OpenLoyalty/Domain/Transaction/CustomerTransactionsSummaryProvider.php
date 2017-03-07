<?php

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
