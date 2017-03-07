<?php

namespace OpenLoyalty\Domain\Account;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use OpenLoyalty\Domain\Account\Event\AccountWasCreated;
use OpenLoyalty\Domain\Account\Event\PointsTransferHasBeenCanceled;
use OpenLoyalty\Domain\Account\Event\PointsTransferHasBeenExpired;
use OpenLoyalty\Domain\Account\Event\PointsWereAdded;
use OpenLoyalty\Domain\Account\Event\PointsWereSpent;
use OpenLoyalty\Domain\Account\Exception\CannotBeCanceledException;
use OpenLoyalty\Domain\Account\Exception\NotEnoughPointsException;
use OpenLoyalty\Domain\Account\Model\AddPointsTransfer;
use OpenLoyalty\Domain\Account\Model\PointsTransfer;
use OpenLoyalty\Domain\Account\Model\SpendPointsTransfer;

/**
 * Class Account.
 */
class Account extends EventSourcedAggregateRoot
{
    /**
     * @var AccountId
     */
    protected $id;

    /**
     * @var CustomerId
     */
    protected $customerId;

    /**
     * @var PointsTransfer[]
     */
    protected $pointsTransfers = [];

    public function getTransferById(PointsTransferId $pointsTransferId)
    {
        if (!isset($this->pointsTransfers[$pointsTransferId->__toString()])) {
            return;
        }

        return $this->pointsTransfers[$pointsTransferId->__toString()];
    }

    public static function createAccount(AccountId $accountId, CustomerId $customerId)
    {
        $account = new self();
        $account->create($accountId, $customerId);

        return $account;
    }

    public function addPoints(AddPointsTransfer $pointsTransfer)
    {
        $this->apply(
            new PointsWereAdded($this->id, $pointsTransfer)
        );
    }

    public function spendPoints(SpendPointsTransfer $pointsTransfer)
    {
        if ($this->getAvailableAmount() < $pointsTransfer->getValue()) {
            throw new NotEnoughPointsException();
        }
        $this->apply(
            new PointsWereSpent($this->id, $pointsTransfer)
        );
    }

    public function cancelPointsTransfer(PointsTransferId $pointsTransferId)
    {
        $this->apply(
            new PointsTransferHasBeenCanceled($this->id, $pointsTransferId)
        );
    }

    public function expirePointsTransfer(PointsTransferId $pointsTransferId)
    {
        $this->apply(
            new PointsTransferHasBeenExpired($this->id, $pointsTransferId)
        );
    }

    /**
     * @return string
     */
    public function getAggregateRootId()
    {
        return $this->id;
    }

    /**
     * @return AccountId
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @param array $pointsTransfers
     */
    public function setPointsTransfers($pointsTransfers)
    {
        $this->pointsTransfers = $pointsTransfers;
    }

    public function getAvailableAmount()
    {
        $sum = 0;

        foreach ($this->getAllActiveAddPointsTransfers() as $pointsTransfer) {
            $sum += $pointsTransfer->getAvailableAmount();
        }

        return $sum;
    }

    private function addPointsTransfer(PointsTransfer $pointsTransfer)
    {
        if (isset($this->pointsTransfers[$pointsTransfer->getId()->__toString()])) {
            throw new \InvalidArgumentException($pointsTransfer->getId()->__toString().' already exists');
        }
        $this->pointsTransfers[$pointsTransfer->getId()->__toString()] = $pointsTransfer;
    }

    private function create(AccountId $accountId, CustomerId $customerId)
    {
        $this->apply(
            new AccountWasCreated($accountId, $customerId)
        );
    }

    protected function applyAccountWasCreated(AccountWasCreated $event)
    {
        $this->id = $event->getAccountId();
        $this->customerId = $event->getCustomerId();
    }

    protected function applyPointsWereAdded(PointsWereAdded $event)
    {
        $this->addPointsTransfer($event->getPointsTransfer());
    }

    protected function applyPointsWereSpent(PointsWereSpent $event)
    {
        $this->addPointsTransfer($event->getPointsTransfer());
        $amount = $event->getPointsTransfer()->getValue();
        foreach ($this->getAllActiveAddPointsTransfers() as $pointsTransfer) {
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
            $this->pointsTransfers[$pointsTransfer->getId()->__toString()] = $pointsTransfer->updateAvailableAmount($availableAmount);
        }
    }

    protected function applyPointsTransferHasBeenCanceled(PointsTransferHasBeenCanceled $event)
    {
        $id = $event->getPointsTransferId();
        if (!isset($this->pointsTransfers[$id->__toString()])) {
            throw new \InvalidArgumentException($id->__toString().' does not exists');
        }
        $transfer = $this->pointsTransfers[$id->__toString()];
        if (!$transfer instanceof AddPointsTransfer) {
            throw new CannotBeCanceledException();
        }
        $this->pointsTransfers[$id->__toString()] = $transfer->cancel();
    }

    protected function applyPointsTransferHasBeenExpired(PointsTransferHasBeenExpired $event)
    {
        $id = $event->getPointsTransferId();
        if (!isset($this->pointsTransfers[$id->__toString()])) {
            throw new \InvalidArgumentException($id->__toString().' does not exists');
        }
        $transfer = $this->pointsTransfers[$id->__toString()];
        if (!$transfer instanceof AddPointsTransfer) {
            throw new \InvalidArgumentException($id->__toString().' cannot be expired');
        }
        $this->pointsTransfers[$id->__toString()] = $transfer->expire();
    }

    /**
     * @return AddPointsTransfer[]
     */
    protected function getAllActiveAddPointsTransfers()
    {
        $transfers = [];
        foreach ($this->pointsTransfers as $pointsTransfer) {
            if (!$pointsTransfer instanceof AddPointsTransfer) {
                continue;
            }
            if ($pointsTransfer->isExpired() || $pointsTransfer->getAvailableAmount() == 0 || $pointsTransfer->isCanceled()) {
                continue;
            }

            $transfers[] = $pointsTransfer;
        }

        usort($transfers, function (PointsTransfer $a, PointsTransfer $b) {
            return $a->getCreatedAt() > $b->getCreatedAt();
        });

        return $transfers;
    }

    /**
     * @param $days
     *
     * @return Model\AddPointsTransfer[]
     */
    protected function getAllNotExpiredAddPointsTransfersOlderThan($days)
    {
        $transfers = [];
        $date = new \DateTime('-'.$days.' days');
        $date->setTime(0, 0, 0);
        foreach ($this->pointsTransfers as $pointsTransfer) {
            if (!$pointsTransfer instanceof AddPointsTransfer) {
                continue;
            }
            if ($pointsTransfer->isExpired() || $pointsTransfer->isCanceled()) {
                continue;
            }
            if ($pointsTransfer->getCreatedAt() >= $date) {
                continue;
            }

            $transfers[] = $pointsTransfer;
        }

        return $transfers;
    }
}
