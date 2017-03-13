<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Customer\ReadModel;

use Broadway\ReadModel\Projector;
use Broadway\ReadModel\RepositoryInterface;
use OpenLoyalty\Domain\Customer\Event\CustomerWasMovedToLevel;
use OpenLoyalty\Domain\Customer\LevelId;
use OpenLoyalty\Domain\Level\Level;
use OpenLoyalty\Domain\Level\LevelRepository;

/**
 * Class CustomersBelongingToOneLevelProjector.
 */
class CustomersBelongingToOneLevelProjector extends Projector
{
    /**
     * @var RepositoryInterface
     */
    private $customerDetailsRepository;

    /**
     * @var RepositoryInterface
     */
    private $customersBelongingToOneLevelRepository;

    /**
     * @var LevelRepository
     */
    private $levelRepository;

    /**
     * CustomersBelongingToOneLevelProjector constructor.
     *
     * @param RepositoryInterface $customerDetailsRepository
     * @param RepositoryInterface $customersBelongingToOneLevelRepository
     * @param LevelRepository     $levelRepository
     */
    public function __construct(
        RepositoryInterface $customerDetailsRepository,
        RepositoryInterface $customersBelongingToOneLevelRepository,
        LevelRepository $levelRepository
    ) {
        $this->customerDetailsRepository = $customerDetailsRepository;
        $this->customersBelongingToOneLevelRepository = $customersBelongingToOneLevelRepository;
        $this->levelRepository = $levelRepository;
    }

    public function applyCustomerWasMovedToLevel(CustomerWasMovedToLevel $event)
    {
        $customerId = $event->getCustomerId();
        $levelId = $event->getLevelId();
        /** @var CustomerDetails $customer */
        $customer = $this->customerDetailsRepository->find($customerId->__toString());
        $currentLevel = $customer->getLevelId();
        if ($currentLevel) {
            $oldReadModel = $this->getReadModel($currentLevel, false);
            if ($oldReadModel) {
                $oldReadModel->removeCustomer($customer);
                $this->customersBelongingToOneLevelRepository->save($oldReadModel);
                /** @var Level $level */
                $level = $this->levelRepository->byId(new \OpenLoyalty\Domain\Level\LevelId($oldReadModel->getLevelId()->__toString()));
                if ($level) {
                    $level->setCustomersCount(count($oldReadModel->getCustomers()));
                    $this->levelRepository->save($level);
                }
            }
        }

        if ($levelId) {
            $readModel = $this->getReadModel($levelId);
            $readModel->addCustomer($customer);
            $customer->setLevelId($levelId);
            if ($event->isManually()) {
                $customer->setManuallyAssignedLevelId($levelId);
            }
            $this->customersBelongingToOneLevelRepository->save($readModel);
            /** @var Level $level */
            $level = $this->levelRepository->byId(new \OpenLoyalty\Domain\Level\LevelId($readModel->getLevelId()->__toString()));
            if ($level) {
                $level->setCustomersCount(count($readModel->getCustomers()));
                $this->levelRepository->save($level);
            }
        } else {
            $customer->setLevelId(null);
            if ($event->isManually()) {
                $customer->setManuallyAssignedLevelId(null)
                ;
            }
        }

        $this->customerDetailsRepository->save($customer);
    }

    private function getReadModel(LevelId $levelId, $createIfNull = true)
    {
        $readModel = $this->customersBelongingToOneLevelRepository->find($levelId->__toString());

        if (null === $readModel && $createIfNull) {
            $readModel = new CustomersBelongingToOneLevel($levelId);
        }

        return $readModel;
    }
}
