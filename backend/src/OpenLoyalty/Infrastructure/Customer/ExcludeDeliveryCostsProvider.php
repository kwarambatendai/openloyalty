<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Infrastructure\Customer;

interface ExcludeDeliveryCostsProvider
{
    /**
     * @return bool
     */
    public function areExcluded();
}
