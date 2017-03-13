<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
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
