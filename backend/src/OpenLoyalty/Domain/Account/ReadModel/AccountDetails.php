<?php

namespace OpenLoyalty\Domain\Account\ReadModel;

use Broadway\ReadModel\ReadModelInterface;
use Broadway\Serializer\SerializableInterface;
use OpenLoyalty\Domain\Account\AccountId;
use OpenLoyalty\Domain\Account\Model\AddPointsTransfer;
use OpenLoyalty\Domain\Account\Model\PointsTransfer;
use OpenLoyalty\Domain\Account\PointsTransferId;
use OpenLoyalty\Domain\Account\CustomerId;

/**
 * Class AccountDetails.
 */
class AccountDetails implements ReadModelInterface, SerializableInterface
{
    /**
     * @var AccountId
     */
    protected $accountId;

    /**
     * @var CustomerId
     */
    protected $customerId;

    /**
     * @var PointsTransfer[]
     */
    protected $transfers = [];

    /**
     * AccountDetails constructor.
     *
     * @param AccountId  $id
     * @param CustomerId $customerId
     */
    public function __construct(AccountId $id, CustomerId $customerId)
    {
        $this->accountId = $id;
        $this->customerId = $customerId;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        $account = new self(new AccountId($data['accountId']), new CustomerId($data['customerId']));
        foreach ($data['transfers'] as $transfer) {
            $account->addPointsTransfer($transfer['type']::deserialize($transfer['data']));
        }

        return $account;
    }

    /**
     * @return array
     */
    public function serialize()
    {
        $transfers = [];
        foreach ($this->transfers as $transfer) {
            $transfers[] = [
                'type' => get_class($transfer),
                'data' => $transfer->serialize(),
            ];
        }

        return [
            'accountId' => $this->accountId->__toString(),
            'customerId' => $this->customerId->__toString(),
            'transfers' => $transfers,
        ];
    }

    public function addPointsTransfer(PointsTransfer $pointsTransfer)
    {
        if (isset($this->transfers[$pointsTransfer->getId()->__toString()])) {
            throw new \InvalidArgumentException($pointsTransfer->getId()->__toString().' already exists');
        }
        $this->transfers[$pointsTransfer->getId()->__toString()] = $pointsTransfer;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->accountId->__toString();
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @param CustomerId $customerId
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     * @return AccountId
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @return AddPointsTransfer[]
     */
    public function getAllActiveAddPointsTransfers()
    {
        $transfers = [];
        foreach ($this->transfers as $pointsTransfer) {
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
     * @return AddPointsTransfer[]
     */
    public function getAllExpiredAddPointsTransfers()
    {
        $transfers = [];
        foreach ($this->transfers as $pointsTransfer) {
            if (!$pointsTransfer instanceof AddPointsTransfer) {
                continue;
            }
            if (!$pointsTransfer->isExpired()) {
                continue;
            }

            $transfers[$pointsTransfer->getCreatedAt()->getTimestamp()] = $pointsTransfer;
        }

        ksort($transfers);

        return $transfers;
    }

    /**
     * @return AddPointsTransfer[]
     */
    public function getAllAddPointsTransfers()
    {
        $transfers = [];
        foreach ($this->transfers as $pointsTransfer) {
            if (!$pointsTransfer instanceof AddPointsTransfer) {
                continue;
            }

            $transfers[$pointsTransfer->getCreatedAt()->getTimestamp()] = $pointsTransfer;
        }

        ksort($transfers);

        return $transfers;
    }

    public function getTransfer(PointsTransferId $pointsTransferId)
    {
        if (!isset($this->transfers[$pointsTransferId->__toString()])) {
            return;
        }

        return $this->transfers[$pointsTransferId->__toString()];
    }

    public function setTransfer(PointsTransfer $pointsTransfer)
    {
        $this->transfers[$pointsTransfer->getId()->__toString()] = $pointsTransfer;
    }

    public function getAvailableAmount()
    {
        $sum = 0;

        foreach ($this->getAllActiveAddPointsTransfers() as $pointsTransfer) {
            $sum += $pointsTransfer->getAvailableAmount();
        }

        return $sum;
    }

    public function getUsedAmount()
    {
        $sum = 0;

        foreach ($this->getAllAddPointsTransfers() as $pointsTransfer) {
            $sum += $pointsTransfer->getUsedAmount();
        }

        return $sum;
    }

    public function getExpiredAmount()
    {
        $sum = 0;

        foreach ($this->getAllExpiredAddPointsTransfers() as $pointsTransfer) {
            $sum += $pointsTransfer->getAvailableAmount();
        }

        return $sum;
    }

    /**
     * @return \OpenLoyalty\Domain\Account\Model\PointsTransfer[]
     */
    public function getTransfers()
    {
        return $this->transfers;
    }
}
