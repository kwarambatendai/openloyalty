<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Account\Command;

use OpenLoyalty\Domain\Account\AccountId;
use OpenLoyalty\Domain\Account\Model\AddPointsTransfer;

/**
 * Class AddPoints.
 */
class AddPoints extends AccountCommand
{
    /**
     * @var AddPointsTransfer
     */
    protected $pointsTransfer;

    public function __construct(AccountId $accountId, AddPointsTransfer $pointsTransfer)
    {
        parent::__construct($accountId);
        $this->pointsTransfer = $pointsTransfer;
    }

    /**
     * @return AddPointsTransfer
     */
    public function getPointsTransfer()
    {
        return $this->pointsTransfer;
    }
}
