<?php

namespace OpenLoyalty\Infrastructure\Customer;

interface ExcludeDeliveryCostsProvider
{
    /**
     * @return bool
     */
    public function areExcluded();
}
