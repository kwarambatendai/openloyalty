<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Segment\Segmentation\CriteriaEvaluators;

use OpenLoyalty\Domain\Transaction\CustomerId;

interface CustomerValidator
{
    /**
     * @param CustomerId $customerId
     *
     * @return bool
     */
    public function isValid(CustomerId $customerId);
}
