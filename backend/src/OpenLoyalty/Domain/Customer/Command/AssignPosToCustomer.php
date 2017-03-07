<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Customer\Command;

use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\PosId;

/**
 * Class AssignPosToCustomer.
 */
class AssignPosToCustomer extends CustomerCommand
{
    /**
     * @var PosId
     */
    protected $posId;

    public function __construct(CustomerId $customerId, PosId $posId)
    {
        parent::__construct($customerId);
        $this->posId = $posId;
    }

    /**
     * @return PosId
     */
    public function getPosId()
    {
        return $this->posId;
    }
}
