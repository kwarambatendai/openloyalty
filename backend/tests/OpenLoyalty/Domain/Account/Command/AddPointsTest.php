<?php

namespace OpenLoyalty\Domain\Account\Command;

use OpenLoyalty\Domain\Account\AccountId;
use OpenLoyalty\Domain\Account\Event\AccountWasCreated;
use OpenLoyalty\Domain\Account\Event\PointsWereAdded;
use OpenLoyalty\Domain\Account\Model\AddPointsTransfer;
use OpenLoyalty\Domain\Account\PointsTransferId;
use OpenLoyalty\Domain\Account\CustomerId;

/**
 * Class AddPointsTest.
 */
class AddPointsTest extends AccountCommandHandlerTest
{
    /**
     * @test
     */
    public function it_add_points_to_account()
    {
        $accountId = new AccountId('00000000-0000-0000-0000-000000000000');
        $customerId = new CustomerId('00000000-1111-0000-0000-000000000000');
        $pointsTransferId = new PointsTransferId('00000000-1111-0000-0000-000000000111');
        $this->scenario
            ->withAggregateId($accountId)
            ->given([
                new AccountWasCreated($accountId, $customerId)
            ])
            ->when(new AddPoints($accountId, new AddPointsTransfer($pointsTransferId, 100)))
            ->then(array(
                new PointsWereAdded($accountId, new AddPointsTransfer($pointsTransferId, 100))
            ));
    }
}
