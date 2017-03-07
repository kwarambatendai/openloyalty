<?php

namespace OpenLoyalty\Domain\Account\Command;

use OpenLoyalty\Domain\Account\AccountId;
use OpenLoyalty\Domain\Account\Model\SpendPointsTransfer;

/**
 * Class SpendPoints.
 */
class SpendPoints extends AccountCommand
{
    /**
     * @var SpendPointsTransfer
     */
    protected $pointsTransfer;

    public function __construct(AccountId $accountId, SpendPointsTransfer $pointsTransfer)
    {
        parent::__construct($accountId);
        $this->pointsTransfer = $pointsTransfer;
    }

    /**
     * @return SpendPointsTransfer
     */
    public function getPointsTransfer()
    {
        return $this->pointsTransfer;
    }
}
