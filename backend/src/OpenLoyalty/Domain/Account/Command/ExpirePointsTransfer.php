<?php

namespace OpenLoyalty\Domain\Account\Command;

use OpenLoyalty\Domain\Account\AccountId;
use OpenLoyalty\Domain\Account\PointsTransferId;

/**
 * Class ExpirePointsTransfer.
 */
class ExpirePointsTransfer extends AccountCommand
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
