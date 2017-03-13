<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyaltyPlugin\SalesManagoBundle\DataProvider;

use OpenLoyalty\Domain\Pos\PosId;
use OpenLoyalty\Domain\Pos\PosRepository;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetailsRepository;
use OpenLoyalty\Domain\Transaction\SystemEvent\CustomerAssignedToTransactionSystemEvent;

/**
 * Class TransactionsDataProvider.
 */
class TransactionsDataProvider implements DataProviderInterface
{
    /**
     * @var CustomerDetailsRepository
     */
    protected $customerDetailsRepository;
    /**
     * @var TransactionDetailsRepository
     */
    protected $transactionDetailsRepository;

    /**
     * @var PosRepository
     */
    protected $posRepository;

    /**
     * SalesManagoContactTagsSender constructor.
     *
     * @param CustomerDetailsRepository    $customerDetailsRepository
     * @param TransactionDetailsRepository $transactionDetailsRepository
     * @param PosRepository                $posRepository
     */
    public function __construct(
        CustomerDetailsRepository $customerDetailsRepository,
        TransactionDetailsRepository $transactionDetailsRepository,
        PosRepository $posRepository
    ) {
        $this->customerDetailsRepository = $customerDetailsRepository;
        $this->transactionDetailsRepository = $transactionDetailsRepository;
        $this->posRepository = $posRepository;
    }

    /**
     * @param CustomerAssignedToTransactionSystemEvent $data
     *
     * @return array|bool
     */
    public function provideData($data)
    {
        $transaction = $this->transactionDetailsRepository->find($data->getTransactionId());

        if (!$transaction->getPosId()) {
            return false;
        }

        $customer = $this->customerDetailsRepository->find($data->getCustomerId());
        $pos = $this->posRepository->byId(new PosId($transaction->getPosId()->__toString()));

        return ['email' => $customer->getEmail(), 'pos' => $pos->getIdentifier()];
    }
}
