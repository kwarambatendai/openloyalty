<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Account\Model;

use OpenLoyalty\Domain\Account\PointsTransferId;
use Assert\Assertion as Assert;
use OpenLoyalty\Domain\Account\TransactionId;

/**
 * Class AddPointsTransfer.
 */
class AddPointsTransfer extends PointsTransfer
{
    /**
     * @var int
     */
    protected $availableAmount;

    /**
     * @var bool
     */
    protected $expired = false;

    /**
     * @var TransactionId
     */
    protected $transactionId;

    /**
     * PointsTransfer constructor.
     *
     * @param PointsTransferId $id
     * @param int              $value
     * @param \DateTime        $createdAt
     * @param bool             $canceled
     * @param TransactionId    $transactionId
     * @param string           $comment
     */
    public function __construct(PointsTransferId $id, $value, \DateTime $createdAt = null, $canceled = false, TransactionId $transactionId = null, $comment = null, $issuer = self::ISSUER_SYSTEM)
    {
        parent::__construct($id, $value, $createdAt, $canceled, $comment, $issuer);
        $this->availableAmount = $value;
        $this->transactionId = $transactionId;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        $createdAt = null;
        if (isset($data['createdAt'])) {
            $createdAt = new \DateTime();
            $createdAt->setTimestamp($data['createdAt']);
        }
        $transfer = new self(new PointsTransferId($data['id']), $data['value'], $createdAt, $data['canceled']);
        if (isset($data['availableAmount'])) {
            Assert::integer($data['availableAmount']);
            Assert::min($data['availableAmount'], 0);
            $transfer->availableAmount = $data['availableAmount'];
        }
        if (isset($data['expired'])) {
            Assert::boolean($data['expired']);
            $transfer->expired = $data['expired'];
        }

        if (isset($data['transactionId'])) {
            $transfer->transactionId = new TransactionId($data['transactionId']);
        }

        if (isset($data['comment'])) {
            $transfer->comment = $data['comment'];
        }
        if (isset($data['issuer'])) {
            $transfer->issuer = $data['issuer'];
        }

        return $transfer;
    }

    public function serialize()
    {
        return array_merge(
            parent::serialize(),
            [
                'availableAmount' => $this->availableAmount,
                'expired' => $this->expired,
                'transactionId' => $this->transactionId ? $this->transactionId->__toString() : null,
            ]
        );
    }

    public function updateAvailableAmount($value)
    {
        Assert::notBlank($value);
        Assert::integer($value);
        Assert::max($value, $this->value);
        $this->availableAmount = $value;

        return $this;
    }

    public function cancel()
    {
        $this->canceled = true;

        return $this;
    }

    public function expire()
    {
        $this->expired = true;

        return $this;
    }

    /**
     * @return int
     */
    public function getAvailableAmount()
    {
        return $this->availableAmount;
    }

    /**
     * @return int
     */
    public function getUsedAmount()
    {
        return $this->value - $this->availableAmount;
    }

    /**
     * @return bool
     */
    public function isExpired()
    {
        return $this->expired;
    }

    /**
     * @return TransactionId
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }
}
