<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\EarningRule;

use OpenLoyalty\Domain\Account\TransactionId;
use OpenLoyalty\Domain\Customer\ReadModel\InvitationDetailsRepository;
use OpenLoyalty\Domain\EarningRule\Algorithm\EarningRuleAlgorithmFactoryInterface;
use OpenLoyalty\Domain\EarningRule\Algorithm\EarningRuleAlgorithmInterface;
use OpenLoyalty\Domain\EarningRule\Algorithm\RuleEvaluationContext;
use OpenLoyalty\Domain\Transaction\CustomerId;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetails;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetailsRepository;
use OpenLoyalty\Infrastructure\Account\EarningRuleApplier;
use OpenLoyalty\Infrastructure\Account\Model\EvaluationResult;
use OpenLoyalty\Infrastructure\Account\Model\ReferralEvaluationResult;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use OpenLoyalty\Domain\Identifier;
use OpenLoyalty\Domain\Segment\ReadModel\SegmentedCustomersRepository;

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
     * @var InvitationDetailsRepository
     */
    protected $invitationDetailsRepository;

    /**
     * @var EarningRuleAlgorithmFactoryInterface
     */
    protected $algorithmFactory;

    /**
     * @var SegmentedCustomersRepository
     */
    protected $segmentedCustomerElasticSearchRepository;

    /**
     * @var CustomerDetailsRepository
     */
    protected $customerDetailsRepository;

    /**
     * OloyEarningRuleEvaluator constructor.
     *
     * @param EarningRuleRepository                $earningRuleRepository
     * @param TransactionDetailsRepository         $transactionDetailsRepository
     * @param EarningRuleAlgorithmFactoryInterface $algorithmFactory
     * @param InvitationDetailsRepository          $invitationDetailsRepository
     * @param SegmentedCustomersRepository         $segmentedCustomerElasticSearchRepository
     * @param CustomerDetailsRepository            $customerDetailsRepository
     */
    public function __construct(
        EarningRuleRepository $earningRuleRepository,
        TransactionDetailsRepository $transactionDetailsRepository,
        EarningRuleAlgorithmFactoryInterface $algorithmFactory,
        InvitationDetailsRepository $invitationDetailsRepository,
        SegmentedCustomersRepository $segmentedCustomerElasticSearchRepository,
        CustomerDetailsRepository $customerDetailsRepository
    ) {
        $this->earningRuleRepository = $earningRuleRepository;
        $this->transactionDetailsRepository = $transactionDetailsRepository;
        $this->algorithmFactory = $algorithmFactory;
        $this->segmentedCustomerElasticSearchRepository = $segmentedCustomerElasticSearchRepository;
        $this->customerDetailsRepository = $customerDetailsRepository;
        $this->invitationDetailsRepository = $invitationDetailsRepository;
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
    protected function getEarningRulesAlgorithms(TransactionDetails $transaction, $customerId)
    {
        $customerData = $this->getCustomerLevelAndSegmentsData($customerId);

        $earningRules = $this->earningRuleRepository->findAllActiveEventRulesBySegmentsAndLevels(
            $transaction->getPurchaseDate(),
            $customerData['segments'],
            $customerData['level']
        );

        $result = [];

        foreach ($earningRules as $earningRule) {

            // ignore event rules (supported by call method)
            if ($earningRule instanceof EventEarningRule || $earningRule instanceof CustomEventEarningRule || $earningRule instanceof ReferralEarningRule) {
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
    public function evaluateTransaction($transaction, $customerId)
    {
        $transaction = $this->getTransactionObject($transaction);

        if (!$transaction) {
            return 0;
        }

        $earningRulesItems = $this->getEarningRulesAlgorithms($transaction, $customerId);
        $context = new RuleEvaluationContext($transaction);

        foreach ($earningRulesItems as $earningRuleItem) {
            /** @var EarningRule $earningRule */
            $earningRule = $earningRuleItem[0];
            /** @var EarningRuleAlgorithmInterface $algorithm */
            $algorithm = $earningRuleItem[1];

            $algorithm->evaluate($context, $earningRule);
        }

        return round((float) array_sum($context->getProducts()), 2);
    }

    /**
     * {@inheritdoc}
     */
    public function evaluateEvent($eventName, $customerId)
    {
        $points = 0;

        $customerData = $this->getCustomerLevelAndSegmentsData($customerId);

        $earningRules = $this->earningRuleRepository->findAllActiveEventRules($eventName, $customerData['segments'], $customerData['level']);

        /** @var EventEarningRule $earningRule */
        foreach ($earningRules as $earningRule) {
            if ($earningRule->getPointsAmount() > $points) {
                $points = $earningRule->getPointsAmount();
            }
        }

        return round((float) $points, 2);
    }

    /**
     * Return number of points for this custom event.
     *
     * @param string $eventName
     * @param string $customerId
     *
     * @return int
     */
    public function evaluateCustomEvent($eventName, $customerId)
    {
        /** @var EvaluationResult $result */
        $result = null;

        /** @var array $customerData */
        $customerData = $this->getCustomerLevelAndSegmentsData($customerId);

        $earningRules = $this->earningRuleRepository->findByCustomEventName($eventName, $customerData['segments'], $customerData['level']);
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

    /**
     * @param string $eventName
     * @param string $customerId
     *
     * @return ReferralEvaluationResult[]
     */
    public function evaluateReferralEvent($eventName, $customerId)
    {
        /** @var ReferralEvaluationResult[] $results */
        $results = [];

        /** @var array $customerData */
        $customerData = $this->getCustomerLevelAndSegmentsData($customerId);

        $invitation = $this->invitationDetailsRepository->findOneByRecipientId(new \OpenLoyalty\Domain\Customer\CustomerId($customerId));

        if (!$invitation) {
            return $results;
        }

        $earningRules = $this->earningRuleRepository->findReferralByEventName($eventName, $customerData['segments'], $customerData['level']);
        if (!$earningRules) {
            return $results;
        }

        /** @var ReferralEarningRule $earningRule */
        foreach ($earningRules as $earningRule) {
            if (!isset($results[$earningRule->getRewardType()]) || $earningRule->getPointsAmount() > $results[$earningRule->getRewardType()]->getPoints()) {
                $results[$earningRule->getRewardType()] = new ReferralEvaluationResult(
                    $earningRule->getEarningRuleId()->__toString(),
                    $earningRule->getPointsAmount(),
                    $earningRule->getRewardType(),
                    $invitation
                );
            }
        }

        return $results;
    }

    /**
     * Get customer level and segments data from transaction.
     *
     * @param string customerId
     *
     * @return array
     */
    protected function getCustomerLevelAndSegmentsData($customerId)
    {
        $result = [
            'level' => null,
            'segments' => [],
        ];

        if ($customerId) {
            $customerId = $customerId instanceof Identifier ? $customerId->__toString() : $customerId;
            $levelId = $this->getCustomerLevelById($customerId);
            $arrayOfSegments = $this->getCustomerSegmentsById($customerId);

            $result = [
                'level' => $levelId,
                'segments' => $arrayOfSegments,
            ];
        }

        return $result;
    }

    /**
     * Get customers segments.
     *
     * @param $customerId
     *
     * @return array
     */
    protected function getCustomerSegmentsById($customerId)
    {
        $arrayOfSegments = [];

        if ($customerId) {
            $arrayOfSegmentsObj = $this->segmentedCustomerElasticSearchRepository
                ->findByParameters(
                    ['customerId' => $customerId],
                    true
                );

            $arrayOfSegments = array_map(
                function ($element) {
                    return $element->getSegmentId();
                },
                $arrayOfSegmentsObj
            );
        }

        return $arrayOfSegments;
    }

    /**
     * Get customers level.
     *
     * @param $customerId
     *
     * @return LevelId
     */
    protected function getCustomerLevelById($customerId)
    {
        $levelId = null;

        if ($customerId) {
            $arrayOfLevelsObj = $this->customerDetailsRepository->findOneByCriteria(['id' => $customerId], 1);

            $arrayOfLevels = array_map(
                function ($element) {
                    return $element->getLevelId();
                },
                $arrayOfLevelsObj
            );

            $levelId = isset($arrayOfLevels[0]) ? $arrayOfLevels[0] : null;
        }

        return $levelId;
    }
}
