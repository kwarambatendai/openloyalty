<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyaltyPlugin\SalesManagoBundle\Event\Listener;

use Doctrine\ORM\EntityRepository;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerAgreementsUpdatedSystemEvent;
use OpenLoyaltyPlugin\SalesManagoBundle\Service\SalesManagoContactSegmentTagsSender;
use OpenLoyaltyPlugin\SalesManagoBundle\Service\SalesManagoValidator;

/**
 * Class CustomerSerializationListener.
 */
class CustomerAgreementUpdateListener
{
    protected $sender;
    protected $repository;

    /**
     * CustomerCreateListener constructor.
     *
     * @param SalesManagoContactSegmentTagsSender $sender
     * @param EntityRepository                    $repository
     */
    public function __construct(SalesManagoContactSegmentTagsSender $sender, EntityRepository $repository)
    {
        $this->sender = $sender;
        $this->repository = $repository;
    }

    /**
     * @param CustomerAgreementsUpdatedSystemEvent $event
     */
    public function onCustomerAgreementUpdate(CustomerAgreementsUpdatedSystemEvent $event)
    {
        if (SalesManagoValidator::verifySalesManagoEnabled($this->repository)) {
            $this->sender->customerAgreementChanged($event);
        }
    }
}
