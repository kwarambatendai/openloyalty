<?php

namespace OpenLoyalty\Domain\Segment\Segmentation\CriteriaEvaluators;

use OpenLoyalty\Domain\Segment\Model\Criteria\LastPurchaseNDaysBefore;
use OpenLoyalty\Domain\Segment\Model\Criterion;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetails;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetailsRepository;

/**
 * Class LastPurchaseNDaysBeforeEvaluator.
 */
class LastPurchaseNDaysBeforeEvaluator implements Evaluator
{
    /**
     * @var TransactionDetailsRepository
     */
    protected $transactionDetailsRepository;

    /**
     * @var CustomerValidator
     */
    protected $customerValidator;

    /**
     * BoughtInPosEvaluator constructor.
     *
     * @param TransactionDetailsRepository $transactionDetailsRepository
     * @param CustomerValidator            $customerValidator
     */
    public function __construct(TransactionDetailsRepository $transactionDetailsRepository, CustomerValidator $customerValidator)
    {
        $this->transactionDetailsRepository = $transactionDetailsRepository;
        $this->customerValidator = $customerValidator;
    }

    /**
     * @param Criterion $criterion
     *
     * @return array
     */
    public function evaluate(Criterion $criterion)
    {
        if (!$criterion instanceof LastPurchaseNDaysBefore) {
            return [];
        }

        $from = new \DateTime();
        $from->setTime(0, 0, 0);
        $from->modify('-'.$criterion->getDays().' days');

        $transactions = $this->transactionDetailsRepository->findInPeriod($from, new \DateTime());

        $customers = [];
        /** @var TransactionDetails $transaction */
        foreach ($transactions as $transaction) {
            if (!$this->customerValidator->isValid($transaction->getCustomerId())) {
                continue;
            }
            $customers[$transaction->getCustomerId()->__toString()] = $transaction->getCustomerId()->__toString();
        }

        return $customers;
    }

    /**
     * @param Criterion $criterion
     *
     * @return bool
     */
    public function support(Criterion $criterion)
    {
        return $criterion instanceof LastPurchaseNDaysBefore;
    }
}
