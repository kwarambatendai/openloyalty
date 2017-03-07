<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\EarningRule;

use OpenLoyalty\Domain\Account\TransactionId;
use OpenLoyalty\Domain\EarningRule\Algorithm\EarningRuleAlgorithmFactoryInterface;
use OpenLoyalty\Domain\EarningRule\Algorithm\EarningRuleAlgorithmInterface;
use OpenLoyalty\Domain\EarningRule\Algorithm\RuleEvaluationContext;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetails;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetailsRepository;
use OpenLoyalty\Infrastructure\Account\EarningRuleApplier;
use OpenLoyalty\Infrastructure\Account\Model\EvaluationResult;

/**
 * Class OloyEarningRuleEvaluator.
 */
class OloyEarningRuleEvaluator implements EarningRuleApplier
{
    /**
     * @var EarningRuleRepository
     */
    protected $earningRuleRepository;

    /**
     * @var TransactionDetailsRepository
     */
    protected $transactionDetailsRepository;

    /**
     * @var EarningRuleAlgorithmFactoryInterface
     */
    protected $algorithmFactory;

    /**
     * OloyEarningRuleEvaluator constructor.
     *
     * @param EarningRuleRepository                $earningRuleRepository
     * @param TransactionDetailsRepository         $transactionDetailsRepository
     * @param EarningRuleAlgorithmFactoryInterface $algorithmFactory
     */
    public function __construct(
        EarningRuleRepository $earningRuleRepository,
        TransactionDetailsRepository $transactionDetailsRepository,
        EarningRuleAlgorithmFactoryInterface $algorithmFactory
    ) {
        $this->earningRuleRepository = $earningRuleRepository;
        $this->transactionDetailsRepository = $transactionDetailsRepository;
        $this->algorithmFactory = $algorithmFactory;
    }

    /**
     * @param TransactionDetails|TransactionId $transaction
     *
     * @return TransactionDetails
     */
    protected function getTransactionObject($transaction)
    {
        if ($transaction instanceof TransactionId) {
            $transaction = $this->transactionDetailsRepository->find($transaction->__toString());
        }

        if ($transaction instanceof TransactionDetails) {
            return $transaction;
        }

        return;
    }

    /**
     * @param TransactionDetails $transaction
     *
     * @return array
     */
    protected function getEarningRulesAlgorithms(TransactionDetails $transaction)
    {
        $earningRules = $this->earningRuleRepository->findAllActive($transaction->getPurchaseDate());
        $result = [];

        foreach ($earningRules as $earningRule) {

            // ignore event rules (supported by call method)
            if ($earningRule instanceof EventEarningRule || $earningRule instanceof CustomEventEarningRule) {
                continue;
            }

            /** @var EarningRuleAlgorithmInterface $algorithm */
            $algorithm = $this->algorithmFactory->getAlgorithm($earningRule);
            $result[] = [
                $earningRule,
                $algorithm,
            ];
        }

        usort(
            $result,
            function ($x, $y) {
                return $x[1]->getPriority() - $y[1]->getPriority();
            }
        );

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function evaluateTransaction($transaction)
    {
        $transaction = $this->getTransactionObject($transaction);

        if (!$transaction) {
            return 0;
        }

        $earningRulesItems = $this->getEarningRulesAlgorithms($transaction);
        $context = new RuleEvaluationContext($transaction);

        foreach ($earningRulesItems as $earningRuleItem) {
            /** @var EarningRule $earningRule */
            $earningRule = $earningRuleItem[0];
            /** @var EarningRuleAlgorithmInterface $algorithm */
            $algorithm = $earningRuleItem[1];

            $algorithm->evaluate($context, $earningRule);
        }

        return (int) array_sum($context->getProducts());
    }

    /**
     * {@inheritdoc}
     */
    public function evaluateEvent($eventName)
    {
        $points = 0;
        $earningRules = $this->earningRuleRepository->findAllActiveEventRules($eventName);

        /** @var EventEarningRule $earningRule */
        foreach ($earningRules as $earningRule) {
            if ($earningRule->getPointsAmount() > $points) {
                $points = $earningRule->getPointsAmount();
            }
        }

        return (int) $points;
    }

    /**
     * Return number of points for this custom event.
     *
     * @param string $eventName
     *
     * @return int
     */
    public function evaluateCustomEvent($eventName)
    {
        /** @var EvaluationResult $result */
        $result = null;

        $earningRules = $this->earningRuleRepository->findByCustomEventName($eventName);
        if (!$earningRules) {
            return 0;
        }

        /** @var EarningRule $earningRule */
        foreach ($earningRules as $earningRule) {
            if (null == $result || $earningRule->getPointsAmount() > $result->getPoints()) {
                $result = new EvaluationResult(
                    $earningRule->getEarningRuleId()->__toString(),
                    $earningRule->getPointsAmount()
                );
            }
        }

        return $result;
    }
}
