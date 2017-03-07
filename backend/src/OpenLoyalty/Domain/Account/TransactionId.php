<?php

namespace OpenLoyalty\Domain\Account;

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
    protected $transactionId;

    /**
     * TransactionId constructor.
     *
     * @param string $transactionId
     */
    public function __construct($transactionId)
    {
        $this->transactionId = $transactionId;
        Assert::string($transactionId);
        Assert::uuid($transactionId);
    }

    public function __toString()
    {
        return $this->transactionId;
    }
}
