<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Account\SystemEvent;

use OpenLoyalty\Domain\Account\AccountId;
use OpenLoyalty\Domain\Account\CustomerId;

/**
 * Class AvailablePointsAmountChangedSystemEvent.
 */
class AvailablePointsAmountChangedSystemEvent extends AccountSystemEvent
{
    const OPERATION_TYPE_ADD = 'add';
    const OPERATION_TYPE_SUBTRACT = 'subtract';
    /**
     * @var int
     */
    protected $currentAmount;

    /**
     * @var CustomerId
     */
    protected $customerId;

    /** @var int */
    protected $amountChange;

    /** @var string */
    protected $operationType;

    /**
     * AvailablePointsAmountChangedSystemEvent constructor.
     *
     * @param AccountId  $accountId
     * @param CustomerId $customerId
     * @param int        $currentAmount
     * @param int        $amountChange
     * @param string     $operationType
     */
    public function __construct(AccountId $accountId, CustomerId $customerId, $currentAmount, $amountChange = 0, $operationType = self::OPERATION_TYPE_SUBTRACT)
    {
        parent::__construct($accountId);
        $this->customerId = $customerId;
        $this->currentAmount = $currentAmount;
        $this->amountChange = $amountChange;
        $this->operationType = $operationType;
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return int
     */
    public function getCurrentAmount()
    {
        return $this->currentAmount;
    }

    /**
     * @return int
     */
    public function getAmountChange()
    {
        return $this->amountChange;
    }

    /**
     * @return string
     */
    public function getOperationType()
    {
        return $this->operationType;
    }
}
