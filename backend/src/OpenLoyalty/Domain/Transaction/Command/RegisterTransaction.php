<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Transaction\Command;

use OpenLoyalty\Domain\Transaction\Model\Item;
use OpenLoyalty\Domain\Transaction\PosId;
use OpenLoyalty\Domain\Transaction\TransactionId;
use Assert\Assertion as Assert;

/**
 * Class RegisterTransaction.
 */
class RegisterTransaction extends TransactionCommand
{
    /**
     * @var array
     */
    protected $transactionData;

    /**
     * @var array
     */
    protected $customerData;

    /**
     * @var Item[]
     */
    protected $items;

    /**
     * @var PosId
     */
    protected $posId;

    /**
     * @var array
     */
    protected $excludedDeliverySKUs;

    /**
     * @var array
     */
    protected $excludedLevelSKUs;

    protected $revisedDocument;

    /**
     * @var array
     */
    protected $excludedCategories;

    private $requiredTransactionFields = [
        'documentNumber',
        'purchasePlace',
        'purchaseDate',
    ];

    private $requiredCustomerFields = [
        'name',
    ];

    private $requiredItemFields = [
        'sku',
        'name',
        'quantity',
        'grossValue',
        'category',
    ];

    public function __construct(
        TransactionId $transactionId,
        array $transactionData,
        array $customerData,
        array $items,
        PosId $posId = null,
        array $excludedDeliverySKUs = null,
        array $excludedLevelSKUs = null,
        array $excludedCategories = null,
        $revisedDocument = null
    ) {
        parent::__construct($transactionId);
        foreach ($this->requiredTransactionFields as $field) {
            Assert::keyExists($transactionData, $field);
        }

        foreach ($this->requiredCustomerFields as $field) {
            Assert::keyExists($customerData, $field);
        }

        foreach ($items as $item) {
            foreach ($this->requiredItemFields as $field) {
                Assert::keyExists($item, $field);
            }
        }

        $this->transactionData = $transactionData;
        $this->customerData = $customerData;
        $this->items = $items;
        $this->posId = $posId;
        $this->excludedDeliverySKUs = $excludedDeliverySKUs;
        $this->excludedLevelSKUs = $excludedLevelSKUs;
        $this->excludedCategories = $excludedCategories;
        $this->revisedDocument = $revisedDocument;
    }

    /**
     * @return array
     */
    public function getTransactionData()
    {
        return $this->transactionData;
    }

    /**
     * @return array
     */
    public function getCustomerData()
    {
        return $this->customerData;
    }

    /**
     * @return \OpenLoyalty\Domain\Transaction\Model\Item[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return PosId
     */
    public function getPosId()
    {
        return $this->posId;
    }

    /**
     * @return array
     */
    public function getExcludedDeliverySKUs()
    {
        return $this->excludedDeliverySKUs;
    }

    /**
     * @return array
     */
    public function getExcludedLevelSKUs()
    {
        return $this->excludedLevelSKUs;
    }

    /**
     * @return array
     */
    public function getExcludedCategories()
    {
        return $this->excludedCategories;
    }

    /**
     */
    public function getRevisedDocument()
    {
        return $this->revisedDocument;
    }
}
