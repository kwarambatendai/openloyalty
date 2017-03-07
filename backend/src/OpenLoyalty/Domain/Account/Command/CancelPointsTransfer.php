<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Account\Command;

use OpenLoyalty\Domain\Account\AccountId;
use OpenLoyalty\Domain\Account\PointsTransferId;

/**
 * Class CancelPointsTransfer.
 */
class CancelPointsTransfer extends AccountCommand
{
    /**
     * @var PointsTransferId
     */
    protected $pointsTransferId;

    public function __construct(AccountId $accountId, PointsTransferId $pointsTransferId)
    {
        parent::__construct($accountId);
        $this->pointsTransferId = $pointsTransferId;
    }

    /**
     * @return PointsTransferId
     */
    public function getPointsTransferId()
    {
        return $this->pointsTransferId;
    }
}
