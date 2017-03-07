<?php

namespace OpenLoyalty\Domain\Transaction;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use OpenLoyalty\Domain\Transaction\Event\CustomerWasAssignedToTransaction;
use OpenLoyalty\Domain\Transaction\Event\TransactionWasRegistered;

/**
 * Class Transaction.
 */
class Transaction extends EventSourcedAggregateRoot
{
    const TYPE_RETURN = 'return';
    const TYPE_SELL = 'sell';

    /**
     * @var TransactionId
     */
    protected $transactionId;

    /**
     * @var CustomerId
     */
    protected $customerId;

    /**
     * @var PosId
     */
    protected $posId;

    protected $excludedDeliverySKUs;

    protected $revisedDocument;

    /**
     * @return string
     */
    public function getAggregateRootId()
    {
        return $this->transactionId;
    }

    public static function createTransaction(
        TransactionId $transactionId,
        array $transactionData,
        array $customerData,
        array $items,
        PosId $posId = null,
        array $excludedDeliverySKUs = null,
        array $excludedLevelSKUs = null,
        array $excludedLevelCategories = null,
        $revisedCustomer = null
    ) {
        $transaction = new self();
        $transaction->create(
            $transactionId,
            $transactionData,
            $customerData,
            $items,
            $posId,
            $excludedDeliverySKUs,
            $excludedLevelSKUs,
            $excludedLevelCategories,
            $revisedCustomer
        );

        return $transaction;
    }

    public function assignCustomerToTransaction(CustomerId $customerId)
    {
        $this->apply(
            new CustomerWasAssignedToTransaction($this->transactionId, $customerId)
        );
    }

    private function create(
        TransactionId $transactionId,
        array $transactionData,
        array $customerData,
        array $items,
        PosId $posId = null,
        array $excludedDeliverySKUs = null,
        array $excludedLevelSKUs = null,
        array $excludedLevelCategories = null,
        $revisedDocument = null
    ) {
        $this->apply(
            new TransactionWasRegistered(
                $transactionId,
                $transactionData,
                $customerData,
                $items,
                $posId,
                $excludedDeliverySKUs,
                $excludedLevelSKUs,
                $excludedLevelCategories,
                $revisedDocument
            )
        );
    }

    protected function applyTransactionWasRegistered(TransactionWasRegistered $event)
    {
        $this->transactionId = $event->getTransactionId();
        $this->posId = $event->getPosId();
    }

    protected function applyCustomerWasAssignedToTransaction(CustomerWasAssignedToTransaction $event)
    {
        $this->customerId = $event->getCustomerId();
    }
}
