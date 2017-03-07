<?php

namespace OpenLoyalty\Bundle\PointsBundle\Service;

use Broadway\CommandHandling\CommandBusInterface;
use OpenLoyalty\Bundle\SettingsBundle\Service\SettingsManager;
use OpenLoyalty\Domain\Account\Command\ExpirePointsTransfer;
use OpenLoyalty\Domain\Account\ReadModel\PointsTransferDetails;
use OpenLoyalty\Domain\Account\ReadModel\PointsTransferDetailsRepository;

/**
 * Class PointsTransfersManager.
 */
class PointsTransfersManager
{
    /**
     * @var CommandBusInterface
     */
    protected $commandBus;

    /**
     * @var PointsTransferDetailsRepository
     */
    protected $pointsTransferDetailsRepository;

    /**
     * @var SettingsManager
     */
    protected $settingsManager;

    /**
     * PointsTransfersManager constructor.
     *
     * @param CommandBusInterface             $commandBus
     * @param PointsTransferDetailsRepository $pointsTransferDetailsRepository
     * @param SettingsManager                 $settingsManager
     */
    public function __construct(
        CommandBusInterface $commandBus,
        PointsTransferDetailsRepository $pointsTransferDetailsRepository,
        SettingsManager $settingsManager
    ) {
        $this->commandBus = $commandBus;
        $this->pointsTransferDetailsRepository = $pointsTransferDetailsRepository;
        $this->settingsManager = $settingsManager;
    }

    public function expireTransfers()
    {
        $allTime = $this->settingsManager->getSettingByKey('allTimeActive');
        if ($allTime->getValue()) {
            return [];
        }
        $days = $this->settingsManager->getSettingByKey('pointsDaysActive');
        if (!$days) {
            $days = 60;
        }
        $date = new \DateTime();
        $date->setTime(0, 0, 0);
        $date->modify('-'.$days->getValue().' days');
        $timestamp = $date->getTimestamp();
        $transfers = $this->pointsTransferDetailsRepository->findAllActiveAddingTransfersCreatedAfter($timestamp);

        /** @var PointsTransferDetails $transfer */
        foreach ($transfers as $transfer) {
            $this->commandBus->dispatch(new ExpirePointsTransfer($transfer->getAccountId(), $transfer->getPointsTransferId()));
        }

        return $transfers;
    }
}
