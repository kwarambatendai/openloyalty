<?php

namespace OpenLoyalty\Infrastructure\Customer;

interface TierAssignTypeProvider
{
    const TYPE_POINTS = 'points';
    const TYPE_TRANSACTIONS = 'transactions';

    /**
     * @return string
     */
    public function getType();
}
