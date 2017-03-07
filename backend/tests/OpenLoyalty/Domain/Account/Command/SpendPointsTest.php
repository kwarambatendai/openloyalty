<?php

namespace OpenLoyalty\Domain\Account\Command;

use OpenLoyalty\Domain\Account\AccountId;
use OpenLoyalty\Domain\Account\Event\AccountWasCreated;
use OpenLoyalty\Domain\Account\Event\PointsTransferHasBeenExpired;
use OpenLoyalty\Domain\Account\Event\PointsWereAdded;
use OpenLoyalty\Domain\Account\Event\PointsWereSpent;
use OpenLoyalty\Domain\Account\Model\AddPointsTransfer;
use OpenLoyalty\Domain\Account\Model\SpendPointsTransfer;
use OpenLoyalty\Domain\Account\PointsTransferId;
use OpenLoyalty\Domain\Account\CustomerId;

/**
 * Class SpendPointsTest.
 */
class SpendPointsTest extends AccountCommandHandlerTest
{
    /**
     * @test
     */
    public function it_spend_points()
    {
        $accountId = new AccountId('00000000-0000-0000-0000-000000000000');
        $customerId = new CustomerId('00000000-1111-0000-0000-000000000000');
        $pointsTransferId = new PointsTransferId('00000000-1111-0000-0000-000000000111');
        $spendPointsTransferId = new PointsTransferId('00000000-1111-0000-0000-000000000222');
        $createdAt = new \DateTime();
        $this->scenario
            ->withAggregateId($accountId)
            ->given([
                new AccountWasCreated($accountId, $customerId),
                new PointsWereAdded($accountId, new AddPointsTransfer($pointsTransferId, 100))
            ])
            ->when(new SpendPoints($accountId, new SpendPointsTransfer($spendPointsTransferId, 10, $createdAt)))
            ->then(array(
                new PointsWereSpent($accountId, new SpendPointsTransfer($spendPointsTransferId, 10, $createdAt))
            ));
    }

    /**
     * @test
     * @expectedException \OpenLoyalty\Domain\Account\Exception\NotEnoughPointsException
     */
    public function it_throws_error_when_not_enough_points()
    {
        $accountId = new AccountId('00000000-0000-0000-0000-000000000000');
        $customerId = new CustomerId('00000000-1111-0000-0000-000000000000');
        $pointsTransferId = new PointsTransferId('00000000-1111-0000-0000-000000000111');
        $spendPointsTransferId = new PointsTransferId('00000000-1111-0000-0000-000000000222');
        $this->scenario
            ->withAggregateId($accountId)
            ->given([
                new AccountWasCreated($accountId, $customerId),
                new PointsWereAdded($accountId, new AddPointsTransfer($pointsTransferId, 100)),
                new PointsWereSpent($accountId, new SpendPointsTransfer($spendPointsTransferId, 10)),
            ])
            ->when(
                new SpendPoints($accountId, new SpendPointsTransfer($spendPointsTransferId, 100))
            )
            ->then([]);
    }

    /**
     * @test
     * @expectedException \OpenLoyalty\Domain\Account\Exception\NotEnoughPointsException
     */
    public function it_throws_error_when_points_expired()
    {
        $accountId = new AccountId('00000000-0000-0000-0000-000000000000');
        $customerId = new CustomerId('00000000-1111-0000-0000-000000000000');
        $pointsTransferIds = [
            new PointsTransferId('00000000-1111-0000-0000-000000000111'),
            new PointsTransferId('00000000-1111-0000-0000-000000000112'),
            new PointsTransferId('00000000-1111-0000-0000-000000000113'),
            new PointsTransferId('00000000-1111-0000-0000-000000000114'),
            new PointsTransferId('00000000-1111-0000-0000-000000000115'),
        ];

        $this->scenario
            ->withAggregateId($accountId)
            ->given([
                new AccountWasCreated($accountId, $customerId),
                new PointsWereAdded($accountId, new AddPointsTransfer($pointsTransferIds[0], 300, new \DateTime('2016-01-01'))),
                new PointsWereAdded($accountId, new AddPointsTransfer($pointsTransferIds[1], 200, new \DateTime('2016-02-01'))),
                new PointsWereSpent($accountId, new SpendPointsTransfer($pointsTransferIds[2], 200, new \DateTime('2016-02-15'))),
                new PointsTransferHasBeenExpired($accountId, $pointsTransferIds[0]),
                new PointsTransferHasBeenExpired($accountId, $pointsTransferIds[1]),
            ])
            ->when(
                new SpendPoints($accountId, new SpendPointsTransfer($pointsTransferIds[3], 1))
            )
            ->then([]);
    }
}
