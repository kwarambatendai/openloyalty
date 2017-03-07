<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Account\ReadModel;

use Broadway\ReadModel\ReadModelInterface;
use Broadway\Serializer\SerializableInterface;
use OpenLoyalty\Domain\Account\AccountId;
use OpenLoyalty\Domain\Account\Model\PointsTransfer;
use OpenLoyalty\Domain\Account\PointsTransferId;
use OpenLoyalty\Domain\Account\CustomerId;
use OpenLoyalty\Domain\Account\TransactionId;

/**
 * Class PointsTransferDetails.
 */
class PointsTransferDetails implements ReadModelInterface, SerializableInterface
{
    const TYPE_ADDING = 'adding';
    const TYPE_SPENDING = 'spending';
    const STATE_CANCELED = 'canceled';
    const STATE_ACTIVE = 'active';
    const STATE_EXPIRED = 'expired';

    /**
     * @var PointsTransferId
     */
    protected $pointsTransferId;

    /**
     * @var AccountId
     */
    protected $accountId;

    /**
     * @var CustomerId
     */
    protected $customerId;

    /**
     * @var string
     */
    protected $customerFirstName;

    /**
     * @var string
     */
    protected $customerLastName;

    /**
     * @var string
     */
    protected $customerLoyaltyCardNumber;

    /**
     * @var string
     */
    protected $customerEmail;

    /**
     * @var string
     */
    protected $customerPhone;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var int
     */
    protected $value = 0;

    /**
     * @var string
     */
    protected $state = self::STATE_ACTIVE;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var TransactionId
     */
    protected $transactionId;

    /**
     * @var string
     */
    protected $posIdentifier;

    /**
     * @var string
     */
    protected $comment;

    protected $issuer = PointsTransfer::ISSUER_SYSTEM;

    /**
     * PointsTransfer constructor.
     *
     * @param PointsTransferId $pointsTransferId
     * @param CustomerId       $customerId
     * @param AccountId        $accountId
     */
    public function __construct(
        PointsTransferId $pointsTransferId,
        CustomerId $customerId,
        AccountId $accountId
    ) {
        $this->pointsTransferId = $pointsTransferId;
        $this->customerId = $customerId;
        $this->accountId = $accountId;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->pointsTransferId->__toString();
    }

    /**
     * @return PointsTransferId
     */
    public function getPointsTransferId()
    {
        return $this->pointsTransferId;
    }

    /**
     * @param array $data
     *
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        $newTransfer = new self(new PointsTransferId($data['id']), new CustomerId($data['customerId']), new AccountId($data['accountId']));
        $newTransfer->customerFirstName = $data['customerFirstName'];
        $newTransfer->customerLastName = $data['customerLastName'];
        $newTransfer->customerPhone = $data['customerPhone'];
        $newTransfer->customerEmail = $data['customerEmail'];
        $newTransfer->customerLoyaltyCardNumber = $data['customerLoyaltyCardNumber'];
        $newTransfer->value = $data['value'];
        $newTransfer->state = $data['state'];
        $newTransfer->type = $data['type'];
        if (isset($data['posIdentifier'])) {
            $newTransfer->posIdentifier = $data['posIdentifier'];
        }
        $createdAt = new \DateTime();
        $createdAt->setTimestamp($data['createdAt']);
        $newTransfer->createdAt = $createdAt;
        if (isset($data['transactionId'])) {
            $newTransfer->transactionId = new TransactionId($data['transactionId']);
        }
        if (isset($data['comment'])) {
            $newTransfer->comment = $data['comment'];
        }
        if (isset($data['issuer'])) {
            $newTransfer->issuer = $data['issuer'];
        }

        return $newTransfer;
    }

    /**
     * @return array
     */
    public function serialize()
    {
        $data = [
            'id' => $this->pointsTransferId->__toString(),
            'customerId' => $this->customerId->__toString(),
            'accountId' => $this->accountId->__toString(),
            'customerFirstName' => $this->customerFirstName,
            'customerLastName' => $this->customerLastName,
            'customerPhone' => $this->customerPhone,
            'customerLoyaltyCardNumber' => $this->customerLoyaltyCardNumber,
            'customerEmail' => $this->customerEmail,
            'value' => $this->value,
            'type' => $this->type,
            'createdAt' => $this->createdAt->getTimestamp(),
            'state' => $this->state,
            'transactionId' => $this->transactionId ? $this->transactionId->__toString() : null,
            'comment' => $this->comment,
            'posIdentifier' => $this->posIdentifier,
            'issuer' => $this->issuer,
        ];

        return $data;
    }

    /**
     * @return AccountId
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return string
     */
    public function getCustomerFirstName()
    {
        return $this->customerFirstName;
    }

    /**
     * @return string
     */
    public function getCustomerLastName()
    {
        return $this->customerLastName;
    }

    /**
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->customerEmail;
    }

    /**
     * @return string
     */
    public function getCustomerPhone()
    {
        return $this->customerPhone;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $customerFirstName
     */
    public function setCustomerFirstName($customerFirstName)
    {
        $this->customerFirstName = $customerFirstName;
    }

    /**
     * @param string $customerLastName
     */
    public function setCustomerLastName($customerLastName)
    {
        $this->customerLastName = $customerLastName;
    }

    /**
     * @param string $customerEmail
     */
    public function setCustomerEmail($customerEmail)
    {
        $this->customerEmail = $customerEmail;
    }

    /**
     * @param string $customerPhone
     */
    public function setCustomerPhone($customerPhone)
    {
        $this->customerPhone = $customerPhone;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param int $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return TransactionId
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @param TransactionId $transactionId
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getCustomerLoyaltyCardNumber()
    {
        return $this->customerLoyaltyCardNumber;
    }

    /**
     * @param string $customerLoyaltyCardNumber
     */
    public function setCustomerLoyaltyCardNumber($customerLoyaltyCardNumber)
    {
        $this->customerLoyaltyCardNumber = $customerLoyaltyCardNumber;
    }

    /**
     * @return string
     */
    public function getPosIdentifier()
    {
        return $this->posIdentifier;
    }

    /**
     * @param string $posIdentifier
     */
    public function setPosIdentifier($posIdentifier)
    {
        $this->posIdentifier = $posIdentifier;
    }

    /**
     * @return mixed
     */
    public function getIssuer()
    {
        return $this->issuer;
    }

    /**
     * @param mixed $issuer
     */
    public function setIssuer($issuer)
    {
        $this->issuer = $issuer;
    }
}
