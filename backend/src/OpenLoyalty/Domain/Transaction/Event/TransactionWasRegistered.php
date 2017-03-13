<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Transaction\Event;

use OpenLoyalty\Domain\Transaction\Model\Item;
use OpenLoyalty\Domain\Transaction\PosId;
use OpenLoyalty\Domain\Transaction\TransactionId;

/**
 * Class TransactionWasRegistered.
 */
class TransactionWasRegistered extends TransactionEvent
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

    /**
     * @var array
     */
    protected $excludedLevelCategories;

    protected $revisedDocument;

    /**
     * TransactionEvent constructor.
     *
     * @param TransactionId $transactionId
     * @param array         $transactionData
     * @param array         $customerData
     * @param Item[]        $items
     * @param PosId         $posId
     * @param array         $excludedDeliverySKUs
     * @param array         $excludedLevelSKUs
     * @param array         $excludedLevelCategories
     * @param null          $revisedDocument
     */
    public function __construct(
        TransactionId $transactionId,
        array $transactionData,
        array $customerData,
        array $items = [],
        PosId $posId = null,
        array $excludedDeliverySKUs = null,
        array $excludedLevelSKUs = null,
        array $excludedLevelCategories = null,
        $revisedDocument = null
    ) {
        parent::__construct($transactionId);
        $itemsObjects = [];
        foreach ($items as $item) {
            if ($item instanceof Item) {
                $itemsObjects[] = $item;
            } else {
                $itemsObjects[] = Item::deserialize($item);
            }
        }

        if (is_numeric($transactionData['purchaseDate'])) {
            $tmp = new \DateTime();
            $tmp->setTimestamp($transactionData['purchaseDate']);
            $transactionData['purchaseDate'] = $tmp;
        }

        $this->transactionData = $transactionData;
        $this->customerData = $customerData;
        $this->items = $itemsObjects;
        $this->posId = $posId;
        $this->excludedDeliverySKUs = $excludedDeliverySKUs;
        $this->excludedLevelSKUs = $excludedLevelSKUs;
        $this->excludedLevelCategories = $excludedLevelCategories;
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
    public function getExcludedLevelCategories()
    {
        return $this->excludedLevelCategories;
    }

    /**
     * @return array
     */
    public function serialize()
    {
        $items = [];
        foreach ($this->items as $item) {
            $items[] = $item->serialize();
        }
        $customerData = $this->customerData;

        $transactionData = $this->transactionData;

        if ($transactionData['purchaseDate'] instanceof \DateTime) {
            $transactionData['purchaseDate'] = $transactionData['purchaseDate']->getTimestamp();
        }

        return array_merge(parent::serialize(), [
            'transactionId' => $this->transactionId->__toString(),
            'transactionData' => $transactionData,
            'customerData' => $customerData,
            'items' => $items,
            'posId' => $this->posId ? $this->posId->__toString() : null,
            'excludedDeliverySKUs' => $this->excludedDeliverySKUs,
            'excludedLevelSKUs' => $this->excludedLevelSKUs,
            'excludedLevelCategories' => $this->excludedLevelCategories,
            'revisedDocument' => $this->revisedDocument,
        ]);
    }

    public static function deserialize(array $data)
    {
        $items = [];
        foreach ($data['items'] as $item) {
            $items[] = Item::deserialize($item);
        }

        $transactionData = $data['transactionData'];
        if (is_numeric($transactionData['purchaseDate'])) {
            $tmp = new \DateTime();
            $tmp->setTimestamp($transactionData['purchaseDate']);
            $transactionData['purchaseDate'] = $tmp;
        }
        $customerData = $data['customerData'];

        return new self(
            new TransactionId($data['transactionId']),
            $transactionData,
            $customerData,
            $items,
            isset($data['posIs']) && $data['posId'] ? new PosId($data['posId']) : null,
            isset($data['excludedDeliverySKUs']) ? $data['excludedDeliverySKUs'] : null,
            isset($data['excludedLevelSKUs']) ? $data['excludedLevelSKUs'] : null,
            isset($data['excludedLevelCategories']) ? $data['excludedLevelCategories'] : null,
            isset($data['revisedDocument']) ? $data['revisedDocument'] : null
        );
    }

    /**
     */
    public function getRevisedDocument()
    {
        return $this->revisedDocument;
    }
}
