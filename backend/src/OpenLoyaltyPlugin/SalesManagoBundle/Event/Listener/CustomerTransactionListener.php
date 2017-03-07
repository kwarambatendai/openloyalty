<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyaltyPlugin\SalesManagoBundle\Event\Listener;

use Doctrine\ORM\EntityRepository;
use OpenLoyalty\Domain\Transaction\SystemEvent\CustomerAssignedToTransactionSystemEvent;
use OpenLoyaltyPlugin\SalesManagoBundle\Service\SalesManagoContactTransactionSender;
use OpenLoyaltyPlugin\SalesManagoBundle\Service\SalesManagoValidator;

/**
 * Class CustomerSerializationListener.
 */
class CustomerTransactionListener
{
    /**
     * @var SalesManagoContactTransactionSender
     */
    protected $sender;
    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * CustomerCreateListener constructor.
     *
     * @param SalesManagoContactTransactionSender $sender
     * @param EntityRepository                    $repository
     */
    public function __construct(SalesManagoContactTransactionSender $sender, EntityRepository $repository)
    {
        $this->sender = $sender;
        $this->repository = $repository;
    }

    /**
     * @param CustomerAssignedToTransactionSystemEvent $event
     */
    public function onCustomerTransactionRegistered($event)
    {
        if (SalesManagoValidator::verifySalesManagoEnabled($this->repository)) {
            $this->sender->customerTransactionRegistered($event);
        }
    }
}
