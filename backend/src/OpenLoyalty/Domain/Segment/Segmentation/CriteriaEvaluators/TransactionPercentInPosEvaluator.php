<?php

namespace OpenLoyalty\Domain\Segment\Segmentation\CriteriaEvaluators;

use OpenLoyalty\Domain\Segment\Model\Criteria\TransactionPercentInPos;
use OpenLoyalty\Domain\Segment\Model\Criterion;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetails;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetailsRepository;

/**
 * Class TransactionPercentInPosEvaluator.
 */
class TransactionPercentInPosEvaluator implements Evaluator
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
        if (!$criterion instanceof TransactionPercentInPos) {
            return [];
        }

        $transactions = $this->transactionDetailsRepository->findAllWithCustomer();

        $customersTransactionCount = [];
        /** @var TransactionDetails $transaction */
        foreach ($transactions as $transaction) {
            if (!$this->customerValidator->isValid($transaction->getCustomerId())) {
                continue;
            }
            if (!isset($customersTransactionCount[$transaction->getCustomerId()->__toString()])) {
                $customersTransactionCount[$transaction->getCustomerId()->__toString()] = [
                    'total' => 0,
                    'inPos' => 0,
                ];
            }
            ++$customersTransactionCount[$transaction->getCustomerId()->__toString()]['total'];
            if ($transaction->getPosId() && $transaction->getPosId()->__toString() == $criterion->getPosId()->__toString()) {
                ++$customersTransactionCount[$transaction->getCustomerId()->__toString()]['inPos'];
            }
        }
        $customers = [];

        foreach ($customersTransactionCount as $key => $value) {
            if ($value['total'] == 0) {
                continue;
            }
            $percent = $value['inPos'] / $value['total'];

            if ($percent >= $criterion->getPercent()) {
                $customers[$key] = $key;
            }
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
        return $criterion instanceof TransactionPercentInPos;
    }
}
