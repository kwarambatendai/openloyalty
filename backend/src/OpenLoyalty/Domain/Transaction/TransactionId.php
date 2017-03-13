<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Transaction;

use Assert\Assertion as Assert;
use OpenLoyalty\Domain\Identifier;

/**
 * Class TransactionId.
 */
class TransactionId implements Identifier
{
    private $transactionId;

    /**
     * @param string $transactionId
     */
    public function __construct($transactionId)
    {
        Assert::string($transactionId);
        Assert::uuid($transactionId);

        $this->transactionId = $transactionId;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->transactionId;
    }
}
