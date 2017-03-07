<?php

namespace OpenLoyalty\Bundle\PointsBundle\Event\Listener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use OpenLoyalty\Bundle\SettingsBundle\Service\SettingsManager;
use OpenLoyalty\Domain\Account\ReadModel\PointsTransferDetails;
use OpenLoyalty\Domain\Pos\Pos;
use OpenLoyalty\Domain\Pos\PosRepository;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetails;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetailsRepository;

/**
 * Class PointsTransferSerializationListener.
 */
class PointsTransferSerializationListener implements EventSubscriberInterface
{
    /**
     * @var SettingsManager
     */
    protected $settingsManager;

    /**
     * @var PosRepository
     */
    protected $posRepository;

    /**
     * @var TransactionDetailsRepository
     */
    protected $transactionDetailsRepository;

    /**
     * PointsTransferSerializationListener constructor.
     *
     * @param SettingsManager              $settingsManager
     * @param PosRepository                $posRepository
     * @param TransactionDetailsRepository $transactionDetailsRepository
     */
    public function __construct(
        SettingsManager $settingsManager,
        PosRepository $posRepository,
        TransactionDetailsRepository $transactionDetailsRepository
    ) {
        $this->settingsManager = $settingsManager;
        $this->posRepository = $posRepository;
        $this->transactionDetailsRepository = $transactionDetailsRepository;
    }

    public static function getSubscribedEvents()
    {
        return array(
            array('event' => 'serializer.post_serialize', 'method' => 'onPostSerialize'),
        );
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        /** @var PointsTransferDetails $transfer */
        $transfer = $event->getObject();

        if ($transfer instanceof PointsTransferDetails) {
            if ($transfer->getPosIdentifier()) {
                $pos = $this->posRepository->oneByIdentifier($transfer->getPosIdentifier());
                if ($pos instanceof Pos) {
                    $event->getVisitor()->addData('posName', $pos->getName());
                }
            }
            $allTime = $this->settingsManager->getSettingByKey('allTimeActive');

            if (!is_null($allTime) && $allTime->getValue()) {
                return;
            }
            $days = $this->settingsManager->getSettingByKey('pointsDaysActive');
            if (!$days || !$days->getValue()) {
                $days = 60;
            } else {
                $days = $days->getValue();
            }
            $created = clone $transfer->getCreatedAt();
            $created->modify('+'.$days.' days');

            $event->getVisitor()->addData('expireAt', $created->format(\DateTime::ISO8601));

            if ($transfer->getTransactionId()) {
                $transaction = $this->transactionDetailsRepository->find($transfer->getTransactionId()->__toString());
                if ($transaction instanceof TransactionDetails) {
                    $event->getVisitor()->setData('transactionDocumentNumber', $transaction->getDocumentNumber());
                }
            }
        }
    }
}
