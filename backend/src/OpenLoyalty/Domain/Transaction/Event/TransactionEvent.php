<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Transaction\Event;

use Broadway\Serializer\SerializableInterface;
use OpenLoyalty\Domain\Transaction\TransactionId;

/**
 * Class TransactionEvent.
 */
abstract class TransactionEvent implements SerializableInterface
{
    /**
     * @var TransactionId
     */
    protected $transactionId;

    /**
     * TransactionEvent constructor.
     *
     * @param TransactionId $transactionId
     */
    public function __construct(TransactionId $transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * @return TransactionId
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'transactionId' => $this->transactionId->__toString(),
        ];
    }
}
