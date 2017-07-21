<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Infrastructure\Account;

use OpenLoyalty\Infrastructure\Account\Model\EvaluationResult;

interface EarningRuleApplier
{
    /**
     * Return number of points for this transaction.
     *
     * @param $transaction
     * @param $customerId
     *
     * @return int
     */
    public function evaluateTransaction($transaction, $customerId);

    /**
     * Return number of points for this event.
     *
     * @param string $eventName
     * @param string $customerId
     *
     * @return int
     */
    public function evaluateEvent($eventName, $customerId);

    /**
     * Return number of points for this custom event.
     *
     * @param string $eventName
     * @param string $customerId
     *
     * @return EvaluationResult
     */
    public function evaluateCustomEvent($eventName, $customerId);

    /**
     * @param string $eventName
     * @param string $customerId
     *
     * @return null|EvaluationResult
     */
    public function evaluateReferralEvent($eventName, $customerId);
}
