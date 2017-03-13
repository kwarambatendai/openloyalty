<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Segment\Segmentation\CriteriaEvaluators;

use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use OpenLoyalty\Domain\Segment\Model\Criteria\TransactionAmount;
use OpenLoyalty\Domain\Segment\Model\Criterion;

/**
 * Class TransactionAmountEvaluator.
 */
class TransactionAmountEvaluator implements Evaluator
{
    /**
     * @var CustomerDetailsRepository
     */
    protected $customerDetailsRepository;

    /**
     * TransactionAmountEvaluator constructor.
     *
     * @param CustomerDetailsRepository $customerDetailsRepository
     */
    public function __construct(CustomerDetailsRepository $customerDetailsRepository)
    {
        $this->customerDetailsRepository = $customerDetailsRepository;
    }

    /**
     * @param Criterion $criterion
     *
     * @return array
     */
    public function evaluate(Criterion $criterion)
    {
        if (!$criterion instanceof TransactionAmount) {
            return [];
        }

        $customers = $this->customerDetailsRepository->findAllWithTransactionAmountBetween(
            $criterion->getFromAmount(),
            $criterion->getToAmount()
        );

        $result = [];

        /** @var CustomerDetails$customer */
        foreach ($customers as $customer) {
            $result[$customer->getCustomerId()->__toString()] = $customer->getCustomerId()->__toString();
        }

        return $result;
    }

    /**
     * @param Criterion $criterion
     *
     * @return bool
     */
    public function support(Criterion $criterion)
    {
        return $criterion instanceof TransactionAmount;
    }
}
