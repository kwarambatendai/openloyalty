<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Infrastructure\Account\SystemEvent\Listener;

use Broadway\CommandHandling\CommandBusInterface;
use Broadway\ReadModel\RepositoryInterface;
use Broadway\UuidGenerator\UuidGeneratorInterface;
use OpenLoyalty\Domain\Account\Command\AddPoints;
use OpenLoyalty\Domain\Account\Model\AddPointsTransfer;
use OpenLoyalty\Domain\Account\PointsTransferId;
use OpenLoyalty\Domain\Account\ReadModel\AccountDetails;
use OpenLoyalty\Domain\Account\TransactionId;
use OpenLoyalty\Domain\Transaction\SystemEvent\CustomerAssignedToTransactionSystemEvent;
use OpenLoyalty\Infrastructure\Account\EarningRuleApplier;

/**
 * Class ApplyEarningRuleToTransactionListener.
 */
class ApplyEarningRuleToTransactionListener
{
    /**
     * @var CommandBusInterface
     */
    protected $commandBus;

    /**
     * @var RepositoryInterface
     */
    protected $accountDetailsRepository;

    /**
     * @var UuidGeneratorInterface
     */
    protected $uuidGenerator;

    /**
     * @var EarningRuleApplier
     */
    protected $earningRuleApplier;

    /**
     * ApplyEarningRuleToTransactionListener constructor.
     *
     * @param CommandBusInterface    $commandBus
     * @param RepositoryInterface    $accountDetailsRepository
     * @param UuidGeneratorInterface $uuidGenerator
     * @param EarningRuleApplier     $earningRuleApplier
     */
    public function __construct(
        CommandBusInterface $commandBus,
        RepositoryInterface $accountDetailsRepository,
        UuidGeneratorInterface $uuidGenerator,
        EarningRuleApplier $earningRuleApplier
    ) {
        $this->commandBus = $commandBus;
        $this->accountDetailsRepository = $accountDetailsRepository;
        $this->uuidGenerator = $uuidGenerator;
        $this->earningRuleApplier = $earningRuleApplier;
    }

    public function onRegisteredTransaction(CustomerAssignedToTransactionSystemEvent $event)
    {
        $customerId = $event->getCustomerId();
        $transactionId = $event->getTransactionId();
        $accounts = $this->accountDetailsRepository->findBy(['customerId' => $customerId->__toString()]);
        if (count($accounts) == 0) {
            return;
        }

        $points = $this->earningRuleApplier->evaluateTransaction(new TransactionId($transactionId->__toString()));

        if ($points <= 0) {
            return;
        }

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
}
