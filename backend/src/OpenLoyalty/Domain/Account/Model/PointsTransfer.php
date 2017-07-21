<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Account\Model;

use Broadway\Serializer\SerializableInterface;
use OpenLoyalty\Domain\Account\PointsTransferId;
use Assert\Assertion as Assert;

/**
 * Class PointsTransfer.
 */
abstract class PointsTransfer implements SerializableInterface
{
    const ISSUER_ADMIN = 'admin';
    const ISSUER_SELLER = 'seller';
    const ISSUER_SYSTEM = 'system';

    /**
     * @var PointsTransferId
     */
    protected $id;

    /**
     * @var string
     */
    protected $comment;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var float
     */
    protected $value;

    /**
     * @var bool
     */
    protected $canceled = false;

    /**
     * @var string
     */
    protected $issuer = self::ISSUER_SYSTEM;

    /**
     * PointsTransfer constructor.
     *
     * @param PointsTransferId $id
     * @param int              $value
     * @param \DateTime        $createdAt
     * @param bool             $canceled
     * @param string|null      $comment
     * @param string           $issuer
     */
    public function __construct(PointsTransferId $id, $value, \DateTime $createdAt = null, $canceled = false, $comment = null, $issuer = self::ISSUER_SYSTEM)
    {
        $this->id = $id;
        Assert::notBlank($value);
        Assert::numeric($value);
        Assert::min($value, 1);
        $this->value = $value;
        if ($createdAt) {
            $this->createdAt = $createdAt;
        } else {
            $this->createdAt = new \DateTime();
        }
        $this->comment = $comment;
        $this->canceled = $canceled;
        $this->issuer = $issuer;
    }

    /**
     * @return PointsTransferId
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return (float) $this->value;
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'id' => $this->id->__toString(),
            'value' => $this->value,
            'createdAt' => $this->createdAt->getTimestamp(),
            'canceled' => $this->canceled,
            'comment' => $this->comment,
            'issuer' => $this->issuer,
        ];
    }

    /**
     * @return bool
     */
    public function isCanceled()
    {
        return $this->canceled;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return string
     */
    public function getIssuer()
    {
        return $this->issuer;
    }
}
