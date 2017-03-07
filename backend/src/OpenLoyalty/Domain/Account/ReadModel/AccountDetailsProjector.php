<?php

namespace OpenLoyalty\Domain\Account\ReadModel;

use Broadway\ReadModel\Projector;
use Broadway\ReadModel\RepositoryInterface;
use OpenLoyalty\Domain\Account\AccountId;
use OpenLoyalty\Domain\Account\Event\AccountWasCreated;
use OpenLoyalty\Domain\Account\Event\PointsTransferHasBeenCanceled;
use OpenLoyalty\Domain\Account\Event\PointsTransferHasBeenExpired;
use OpenLoyalty\Domain\Account\Event\PointsWereAdded;
use OpenLoyalty\Domain\Account\Event\PointsWereSpent;
use OpenLoyalty\Domain\Account\Exception\CannotBeCanceledException;
use OpenLoyalty\Domain\Account\Model\AddPointsTransfer;
use OpenLoyalty\Domain\Account\CustomerId;

/**
 * Class AccountDetailsProjector.
 */
class AccountDetailsProjector extends Projector
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * AccountDetailsProjector constructor.
     *
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param AccountWasCreated $event
     */
    protected function applyAccountWasCreated(AccountWasCreated $event)
    {
        $readModel = $this->getReadModel($event->getAccountId(), $event->getCustomerId());
        $this->repository->save($readModel);
    }

    protected function applyPointsWereAdded(PointsWereAdded $event)
    {
        /** @var AccountDetails $readModel */
        $readModel = $this->getReadModel($event->getAccountId());
        $readModel->addPointsTransfer($event->getPointsTransfer());
        $this->repository->save($readModel);
    }

    protected function applyPointsWereSpent(PointsWereSpent $event)
    {
        /** @var AccountDetails $readModel */
        $readModel = $this->getReadModel($event->getAccountId());
        $readModel->addPointsTransfer($event->getPointsTransfer());
        $amount = $event->getPointsTransfer()->getValue();
        foreach ($readModel->getAllActiveAddPointsTransfers() as $pointsTransfer) {
            if ($amount <= 0) {
                break;
            }
            $availableAmount = $pointsTransfer->getAvailableAmount();
            if ($availableAmount > $amount) {
                $availableAmount -= $amount;
                $amount = 0;
            } else {
                $amount -= $availableAmount;
                $availableAmount = 0;
            }
            $readModel->setTransfer($pointsTransfer->updateAvailableAmount($availableAmount));
        }
        $this->repository->save($readModel);
    }

    protected function applyPointsTransferHasBeenCanceled(PointsTransferHasBeenCanceled $event)
    {
        /** @var AccountDetails $readModel */
        $readModel = $this->getReadModel($event->getAccountId());
        $id = $event->getPointsTransferId();
        $transfer = $readModel->getTransfer($id);
        if (!$transfer) {
            throw new \InvalidArgumentException($id->__toString().' does not exists');
        }
        if (!$transfer instanceof AddPointsTransfer) {
            throw new CannotBeCanceledException();
        }
        $readModel->setTransfer($transfer->cancel());
        $this->repository->save($readModel);
    }

    protected function applyPointsTransferHasBeenExpired(PointsTransferHasBeenExpired $event)
    {
        /** @var AccountDetails $readModel */
        $readModel = $this->getReadModel($event->getAccountId());
        $id = $event->getPointsTransferId();
        $transfer = $readModel->getTransfer($id);
        if (!$transfer) {
            throw new \InvalidArgumentException($id->__toString().' does not exists');
        }
        if (!$transfer instanceof AddPointsTransfer) {
            throw new \InvalidArgumentException($id->__toString().' cannot be expired');
        }
        $readModel->setTransfer($transfer->expire());
        $this->repository->save($readModel);
    }

    /**
     * @param AccountId       $accountId
     * @param CustomerId|null $customerId
     *
     * @return \Broadway\ReadModel\ReadModelInterface|null|PointsTransferDetails
     */
    private function getReadModel(AccountId $accountId, CustomerId $customerId = null)
    {
        $readModel = $this->repository->find($accountId->__toString());

        if (null === $readModel && $customerId) {
            $readModel = new AccountDetails($accountId, $customerId);
        }

        return $readModel;
    }
}
