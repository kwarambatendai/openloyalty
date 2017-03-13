<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyaltyPlugin\SalesManagoBundle\Service;

use OpenLoyalty\Domain\Pos\PosRepository;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetailsRepository;
use OpenLoyalty\Domain\Transaction\SystemEvent\CustomerAssignedToTransactionSystemEvent;

/**
 * Class SalesManagoContactTransactionSender.
 */
class SalesManagoContactTransactionSender extends SalesManagoContactSender
{
    /**
     * @var
     */
    protected $ownerEmail;
    /**
     * @var
     */
    protected $settingsManager;
    /**
     * @var CustomerDetailsRepository
     */
    protected $customerDetailsRepository;
    /**
     * @var
     */
    protected $levelRepository;

    /**
     * @var TransactionDetailsRepository
     */
    protected $transactionDetailsRepository;

    /**
     * @var PosRepository
     */
    protected $posRepository;

    /**
     * @param CustomerAssignedToTransactionSystemEvent $event
     */
    public function customerTransactionRegistered($event)
    {
        if (empty($this->getConnector())) {
            return;
        }
        $data = $this->dataProvider->provideData($event);
        $this->send($data['email'], $this->buildTag($data['pos']));
    }
}
