<?php

namespace OpenLoyalty\Domain\Transaction\SystemEvent;

/**
 * Class TransactionSystemEvents.
 */
class TransactionSystemEvents
{
    const TRANSACTION_REGISTERED = 'oloy.transaction.registered';
    const CUSTOMER_ASSIGNED_TO_TRANSACTION = 'oloy.transaction.customer_assigned';
    const CUSTOMER_FIRST_TRANSACTION = 'oloy.transaction.customer_first_transaction';
}
