<?php

namespace OpenLoyalty\Infrastructure\Account\SytemEvent\Listener;

use OpenLoyalty\Domain\Account\AccountId;
use OpenLoyalty\Domain\Account\Command\AddPoints;
use OpenLoyalty\Domain\Account\Model\AddPointsTransfer;
use OpenLoyalty\Domain\Account\PointsTransferId;
use OpenLoyalty\Domain\Transaction\CustomerId;
use OpenLoyalty\Domain\Transaction\SystemEvent\CustomerAssignedToTransactionSystemEvent;
use OpenLoyalty\Domain\Transaction\TransactionId;
use OpenLoyalty\Infrastructure\Account\SystemEvent\Listener\ApplyEarningRuleToTransactionListener;

/**
 * Class ApplyEarningRuleToTransactionListenerTest.
 */
class ApplyEarningRuleToTransactionListenerTest extends BaseApplyEarningRuleListenerTest
{
    /**
     * @test
     */
    public function it_adds_points_on_new_transaction()
    {
        $accountId = new AccountId($this->uuid);
        $expected = new AddPoints($accountId, new AddPointsTransfer(
            new PointsTransferId($this->uuid),
            10,
            null,
            false,
            new \OpenLoyalty\Domain\Account\TransactionId($this->uuid)
        ));

        $listener = new ApplyEarningRuleToTransactionListener(
            $this->getCommandBus($expected),
            $this->getAccountDetailsRepository(),
            $this->getUuidGenerator(),
            $this->getApplierForTransaction(10)
        );

        $listener->onRegisteredTransaction(new CustomerAssignedToTransactionSystemEvent(
            new TransactionId($this->uuid),
            new CustomerId($this->uuid),
            0,
            0
        ));
    }
}
