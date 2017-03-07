<?php

namespace OpenLoyalty\Infrastructure\Account;

use OpenLoyalty\Infrastructure\Account\Model\EvaluationResult;

interface EarningRuleApplier
{
    /**
     * Return number of points for this transaction.
     *
     * @param $transaction
     *
     * @return int
     */
    public function evaluateTransaction($transaction);

    /**
     * Return number of points for this event.
     *
     * @param string $eventName
     *
     * @return int
     */
    public function evaluateEvent($eventName);

    /**
     * Return number of points for this custom event.
     *
     * @param string $eventName
     *
     * @return EvaluationResult
     */
    public function evaluateCustomEvent($eventName);
}
