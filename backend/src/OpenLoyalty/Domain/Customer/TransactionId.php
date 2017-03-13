<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Customer;

use OpenLoyalty\Domain\Identifier;
use Assert\Assertion as Assert;

/**
 * Class TransactionId.
 */
class TransactionId implements Identifier
{
    /**
     * @var string
     */
    private $transactionId;

    /**
     * TransactionId constructor.
     *
     * @param string $transactionId
     */
    public function __construct($transactionId)
    {
        Assert::string($transactionId);
        Assert::uuid($transactionId);
        $this->transactionId = $transactionId;
    }

    public function __toString()
    {
        return $this->transactionId;
    }
}
