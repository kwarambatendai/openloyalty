<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Transaction\Event;

use OpenLoyalty\Domain\Transaction\CustomerId;
use OpenLoyalty\Domain\Transaction\TransactionId;

/**
 * Class CustomerWasAssignedToTransaction.
 */
class CustomerWasAssignedToTransaction extends TransactionEvent
{
    /**
     * @var CustomerId
     */
    protected $customerId;

    public function __construct(TransactionId $transactionId, CustomerId $customerId)
    {
        parent::__construct($transactionId);
        $this->customerId = $customerId;
    }

    public function serialize()
    {
        return array_merge(parent::serialize(), [
            'customerId' => $this->customerId->__toString(),
        ]);
    }

    /**
     * @param array $data
     *
     * @return CustomerWasAssignedToTransaction
     */
    public static function deserialize(array $data)
    {
        return new self(new TransactionId($data['transactionId']), new CustomerId($data['customerId']));
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }
}
