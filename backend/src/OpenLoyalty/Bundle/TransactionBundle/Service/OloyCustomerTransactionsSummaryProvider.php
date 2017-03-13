<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\TransactionBundle\Service;

use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use OpenLoyalty\Domain\Transaction\CustomerId;
use OpenLoyalty\Domain\Transaction\CustomerTransactionsSummaryProvider;

/**
 * Class OloyCustomerTransactionsSummaryProvider.
 */
class OloyCustomerTransactionsSummaryProvider implements CustomerTransactionsSummaryProvider
{
    /**
     * @var CustomerDetailsRepository
     */
    protected $customerDetailsRepository;

    /**
     * OloyCustomerTransactionsSummaryProvider constructor.
     *
     * @param CustomerDetailsRepository $customerDetailsRepository
     */
    public function __construct(CustomerDetailsRepository $customerDetailsRepository)
    {
        $this->customerDetailsRepository = $customerDetailsRepository;
    }

    /**
     * @param CustomerId $customerId
     *
     * @return int
     */
    public function getTransactionsCount(CustomerId $customerId)
    {
        $details = $this->customerDetailsRepository->find($customerId->__toString());
        if (!$details instanceof CustomerDetails) {
            return 0;
        }

        return $details->getTransactionsCount();
    }
}
