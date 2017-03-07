<?php

namespace OpenLoyalty\Infrastructure\Customer\SystemEvent\Listener;

use Broadway\CommandHandling\CommandBusInterface;
use Broadway\EventDispatcher\EventDispatcherInterface;
use OpenLoyalty\Domain\Account\SystemEvent\AccountCreatedSystemEvent;
use OpenLoyalty\Domain\Account\SystemEvent\AvailablePointsAmountChangedSystemEvent;
use OpenLoyalty\Domain\Customer\Command\MoveCustomerToLevel;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\LevelId;
use OpenLoyalty\Domain\Customer\LevelIdProvider;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerLevelChangedSystemEvent;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerSystemEvents;
use OpenLoyalty\Domain\Customer\TransactionId;
use OpenLoyalty\Domain\Level\Level;
use OpenLoyalty\Domain\Level\LevelRepository;
use OpenLoyalty\Domain\Transaction\SystemEvent\CustomerAssignedToTransactionSystemEvent;
use OpenLoyalty\Infrastructure\Customer\ExcludeDeliveryCostsProvider;
use OpenLoyalty\Infrastructure\Customer\TierAssignTypeProvider;

/**
 * Class CalculateCustomerLevelListener.
 */
class CalculateCustomerLevelListener
{
    /**
     * @var LevelIdProvider
     */
    protected $levelIdProvider;

    /**
     * @var CustomerDetailsRepository
     */
    protected $customerDetailsRepository;

    /**
     * @var CommandBusInterface
     */
    protected $commandBus;

    /**
     * @var TierAssignTypeProvider
     */
    protected $tierAssignTypeProvider;

    /**
     * @var ExcludeDeliveryCostsProvider
     */
    protected $excludeDeliveryCostsProvider;

    /** @var LevelRepository */
    protected $levelRepository;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * CalculateCustomerLevelListener constructor.
     *
     * @param LevelIdProvider              $levelIdProvider
     * @param CustomerDetailsRepository    $customerDetailsRepository
     * @param CommandBusInterface          $commandBus
     * @param TierAssignTypeProvider       $tierAssignTypeProvider
     * @param ExcludeDeliveryCostsProvider $excludeDeliveryCostsProvider
     * @param LevelRepository              $levelRepository
     * @param EventDispatcherInterface     $eventDispatcher
     */
    public function __construct(
        LevelIdProvider $levelIdProvider,
        CustomerDetailsRepository $customerDetailsRepository,
        CommandBusInterface $commandBus,
        TierAssignTypeProvider $tierAssignTypeProvider,
        ExcludeDeliveryCostsProvider $excludeDeliveryCostsProvider,
        LevelRepository $levelRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->levelIdProvider = $levelIdProvider;
        $this->customerDetailsRepository = $customerDetailsRepository;
        $this->commandBus = $commandBus;
        $this->tierAssignTypeProvider = $tierAssignTypeProvider;
        $this->excludeDeliveryCostsProvider = $excludeDeliveryCostsProvider;
        $this->levelRepository = $levelRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function handle($event)
    {
        if ($event instanceof AccountCreatedSystemEvent) {
            $this->handleAccountCreated($event);
        } elseif ($this->tierAssignTypeProvider->getType() == TierAssignTypeProvider::TYPE_POINTS && $event instanceof AvailablePointsAmountChangedSystemEvent) {
            $this->handlePoints($event);
        } elseif ($this->tierAssignTypeProvider->getType() == TierAssignTypeProvider::TYPE_TRANSACTIONS && $event instanceof CustomerAssignedToTransactionSystemEvent) {
            $this->handleTransaction($event);
        }
    }

    protected function handleTransaction(CustomerAssignedToTransactionSystemEvent $event)
    {
        $transactionId = $event->getTransactionId();
        $customerId = $event->getCustomerId();

        /** @var CustomerDetails $customer */
        $customer = $this->customerDetailsRepository->find($customerId->__toString());

        if (!$customer) {
            return;
        }

        if ($this->excludeDeliveryCostsProvider->areExcluded()) {
            $currentAmount = $customer->getTransactionsAmountWithoutDeliveryCosts() - $customer->getAmountExcludedForLevel();
            if (!$customer->hasTransactionId(new TransactionId($transactionId->__toString()))) {
                $currentAmount += $event->getGrossValueWithoutDeliveryCosts() - $event->getAmountExcludedForLevel();
            }
        } else {
            $currentAmount = $customer->getTransactionsAmount() - $customer->getAmountExcludedForLevel();

            if (!$customer->hasTransactionId(new TransactionId($transactionId->__toString()))) {
                $currentAmount += $event->getGrossValue() - $event->getAmountExcludedForLevel();
            }
        }

        /** @var Level $currentLevel */
        $currentLevel = $customer->getLevelId()
            ? $this->levelRepository->byId(new \OpenLoyalty\Domain\Level\LevelId($customer->getLevelId()->__toString()))
            : null;

        $levelId = $this->levelIdProvider->findLevelIdByConditionValueWithTheBiggestReward($currentAmount);
        if (!$levelId) {
            return;
        }
        /** @var Level $level */
        $level = $this->levelRepository->byId(new \OpenLoyalty\Domain\Level\LevelId($levelId));

        // if new level is better than old one -> move customer
        if (!$currentLevel || $currentLevel->getReward()->getValue() < $level->getReward()->getValue()) {
            if (!$customer->getLevelId() || $customer->getLevelId()->__toString() != $levelId) {
                $this->commandBus->dispatch(
                    new MoveCustomerToLevel(
                        new CustomerId($customerId->__toString()),
                        new LevelId($levelId)
                    )
                );

                $this->eventDispatcher->dispatch(CustomerSystemEvents::CUSTOMER_LEVEL_CHANGED, [
                    new CustomerLevelChangedSystemEvent($customer->getCustomerId(), new LevelId($levelId)),
                ]);
            }

            return;
        }
        // new level is worst
        $newLevelId = $levelId;

        if ($customer->getManuallyAssignedLevelId()) {
            $manualId = $customer->getManuallyAssignedLevelId()->__toString();
            if ($manualId == $currentLevel->getLevelId()->__toString()) {
                return;
            }
            /** @var Level $manual */
            $manual = $this->levelRepository->byId(new \OpenLoyalty\Domain\Level\LevelId($manualId));
            if ($manual->getReward()->getValue() > $level->getReward()->getValue()) {
                $newLevelId = $manualId;
            }
        }

        $this->commandBus->dispatch(
            new MoveCustomerToLevel(
                new CustomerId($customerId->__toString()),
                new LevelId($newLevelId)
            )
        );

        $this->eventDispatcher->dispatch(CustomerSystemEvents::CUSTOMER_LEVEL_CHANGED, [
            new CustomerLevelChangedSystemEvent($customer->getCustomerId(), new LevelId($newLevelId)),
        ]);
    }

    protected function handlePoints(AvailablePointsAmountChangedSystemEvent $event)
    {
        $customerId = $event->getCustomerId();
        $currentAmount = $event->getCurrentAmount();

        /** @var CustomerDetails $customer */
        $customer = $this->customerDetailsRepository->find($customerId->__toString());

        /** @var Level $currentLevel */
        $currentLevel = $customer->getLevelId()
            ? $this->levelRepository->byId(new \OpenLoyalty\Domain\Level\LevelId($customer->getLevelId()->__toString()))
            : null;

        $levelId = $this->levelIdProvider->findLevelIdByConditionValueWithTheBiggestReward($currentAmount);
        if (!$levelId) {
            return;
        }

        /** @var Level $level */
        $level = $this->levelRepository->byId(new \OpenLoyalty\Domain\Level\LevelId($levelId));

        if ($currentLevel && $currentLevel->getReward()->getValue() >= $level->getReward()->getValue()) {
            return;
        }

        if (!$customer->getLevelId() || $customer->getLevelId()->__toString() != $levelId) {
            $this->commandBus->dispatch(
                new MoveCustomerToLevel(
                    new CustomerId($customerId->__toString()),
                    new LevelId($levelId)
                )
            );

            $this->eventDispatcher->dispatch(CustomerSystemEvents::CUSTOMER_LEVEL_CHANGED, [
                new CustomerLevelChangedSystemEvent($customer->getCustomerId(), new LevelId($levelId)),
            ]);
        }
    }

    protected function handleAccountCreated(AccountCreatedSystemEvent $event)
    {
        $customerId = $event->getCustomerId();
        if (!$customerId) {
            return;
        }
        $currentAmount = 0;
        /** @var CustomerDetails $customer */
        $customer = $this->customerDetailsRepository->find($customerId->__toString());

        if ($customer->getLevelId()) {
            return;
        }

        $levelId = $this->levelIdProvider->findLevelIdByConditionValueWithTheBiggestReward($currentAmount);
        if (!$levelId) {
            $this->commandBus->dispatch(
                new MoveCustomerToLevel(
                    new CustomerId($customerId->__toString()),
                    null
                )
            );

            return;
        }

        if (!$customer->getLevelId() || $customer->getLevelId()->__toString() != $levelId) {
            $this->commandBus->dispatch(
                new MoveCustomerToLevel(
                    new CustomerId($customerId->__toString()),
                    new LevelId($levelId)
                )
            );
        }
    }
}
