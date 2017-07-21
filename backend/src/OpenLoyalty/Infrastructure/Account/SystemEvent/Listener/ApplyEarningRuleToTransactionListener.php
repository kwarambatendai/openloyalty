<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Infrastructure\Account\SystemEvent\Listener;

use OpenLoyalty\Domain\Account\Command\AddPoints;
use OpenLoyalty\Domain\Account\Model\AddPointsTransfer;
use OpenLoyalty\Domain\Account\PointsTransferId;
use OpenLoyalty\Domain\Account\ReadModel\AccountDetails;
use OpenLoyalty\Domain\Account\TransactionId;
use OpenLoyalty\Domain\EarningRule\ReferralEarningRule;
use OpenLoyalty\Domain\Transaction\SystemEvent\CustomerAssignedToTransactionSystemEvent;
use OpenLoyalty\Infrastructure\Account\EarningRuleApplier;

/**
 * Class ApplyEarningRuleToTransactionListener.
 */
class ApplyEarningRuleToTransactionListener extends BaseApplyEarningRuleListener
{
    public function onRegisteredTransaction(CustomerAssignedToTransactionSystemEvent $event)
    {
        $customerId = $event->getCustomerId();
        $transactionId = $event->getTransactionId();
        $accounts = $this->accountDetailsRepository->findBy(['customerId' => $customerId->__toString()]);
        if (count($accounts) == 0) {
            return;
        }

        $points = $this->earningRuleApplier->evaluateTransaction(new TransactionId($transactionId->__toString()), $customerId->__toString());

        if ($points > 0) {
            /** @var AccountDetails $account */
            $account = reset($accounts);
            $this->commandBus->dispatch(
                new AddPoints($account->getAccountId(), new AddPointsTransfer(
                    new PointsTransferId($this->uuidGenerator->generate()),
                    $points,
                    null,
                    false,
                    new TransactionId($transactionId->__toString())
                ))
            );
        }

        if (null !== $event->getTransactionsCount() && $event->getTransactionsCount() != 0) {
            $this->evaluateReferral(ReferralEarningRule::EVENT_EVERY_PURCHASE, $event->getCustomerId()->__toString());
        }
    }
}
