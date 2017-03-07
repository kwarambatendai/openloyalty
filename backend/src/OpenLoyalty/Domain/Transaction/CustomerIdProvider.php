<?php

namespace OpenLoyalty\Domain\Transaction;

interface CustomerIdProvider
{
    /**
     * @param array $customerData
     *
     * @return string
     */
    public function getId(array $customerData);
}
