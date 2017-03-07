<?php

namespace OpenLoyalty\Domain\Account\Command;

use OpenLoyalty\Domain\Account\AccountId;
use OpenLoyalty\Domain\Account\Event\AccountWasCreated;
use OpenLoyalty\Domain\Account\Event\PointsTransferHasBeenExpired;
use OpenLoyalty\Domain\Account\Event\PointsWereAdded;
use OpenLoyalty\Domain\Account\Model\AddPointsTransfer;
use OpenLoyalty\Domain\Account\PointsTransferId;
use OpenLoyalty\Domain\Account\CustomerId;

/**
 * Class ExpirePointsTransferTest.
 */
class ExpirePointsTransferTest extends AccountCommandHandlerTest
{
    /**
     * @test
     */
    public function it_expire_points()
    {
        $accountId = new AccountId('00000000-0000-0000-0000-000000000000');
        $customerId = new CustomerId('00000000-1111-0000-0000-000000000000');
        $pointsTransferId = new PointsTransferId('00000000-1111-0000-0000-000000000111');
        $pointsTransferId2 = new PointsTransferId('00000000-1111-0000-0000-000000000112');
        $this->scenario
            ->withAggregateId($accountId)
            ->given([
                new AccountWasCreated($accountId, $customerId),
                new PointsWereAdded($accountId, new AddPointsTransfer($pointsTransferId, 100, new \DateTime('-11 days'))),
                new PointsWereAdded($accountId, new AddPointsTransfer($pointsTransferId2, 100, new \DateTime('-10 days'))),
            ])
            ->when(new ExpirePointsTransfer($accountId, $pointsTransferId))
            ->then(array(
                new PointsTransferHasBeenExpired($accountId, $pointsTransferId)
            ));
    }
}
