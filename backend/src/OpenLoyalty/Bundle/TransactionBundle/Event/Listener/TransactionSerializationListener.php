<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\TransactionBundle\Event\Listener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use OpenLoyalty\Bundle\SettingsBundle\Service\SettingsManager;
use OpenLoyalty\Domain\Account\ReadModel\PointsTransferDetails;
use OpenLoyalty\Domain\Account\ReadModel\PointsTransferDetailsRepository;
use OpenLoyalty\Domain\Pos\Pos;
use OpenLoyalty\Domain\Pos\PosId;
use OpenLoyalty\Domain\Pos\PosRepository;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetails;

/**
 * Class TransactionSerializationListener.
 */
class TransactionSerializationListener implements EventSubscriberInterface
{
    /**
     * @var PointsTransferDetailsRepository
     */
    protected $transfersRepo;

    /**
     * @var SettingsManager
     */
    protected $settingsManager;

    /**
     * @var PosRepository
     */
    protected $posRepository;

    /**
     * TransactionSerializationListener constructor.
     *
     * @param PointsTransferDetailsRepository $transfersRepo
     * @param SettingsManager                 $settingsManager
     * @param PosRepository                   $posRepository
     */
    public function __construct(
        PointsTransferDetailsRepository $transfersRepo,
        SettingsManager $settingsManager,
        PosRepository $posRepository
    ) {
        $this->transfersRepo = $transfersRepo;
        $this->settingsManager = $settingsManager;
        $this->posRepository = $posRepository;
    }

    public static function getSubscribedEvents()
    {
        return array(
            array('event' => 'serializer.post_serialize', 'method' => 'onPostSerialize'),
        );
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        /** @var TransactionDetails $transaction */
        $transaction = $event->getObject();

        if ($transaction instanceof TransactionDetails) {
            $currency = $this->settingsManager->getSettingByKey('currency');
            $currency = $currency ? $currency->getValue() : 'PLN';
            $event->getVisitor()->addData('currency', $currency);

            $transfers = $this->transfersRepo->findBy([
                'transactionId' => $transaction->getTransactionId()->__toString(),
                'state' => PointsTransferDetails::STATE_ACTIVE,
                'type' => PointsTransferDetails::TYPE_ADDING,
            ]);

            if (count($transfers) > 0) {
                $event->getVisitor()->addData('pointsEarned', array_reduce($transfers, function ($carry, PointsTransferDetails $transfer) {
                    $carry += $transfer->getValue();

                    return $carry;
                }));
            }

            if ($transaction->getPosId()) {
                $pos = $this->posRepository->byId(new PosId($transaction->getPosId()->__toString()));
                if ($pos instanceof Pos) {
                    $event->getVisitor()->addData('posName', $pos->getName());
                }
            }
        }
    }
}
