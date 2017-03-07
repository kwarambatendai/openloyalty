<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyaltyPlugin\SalesManagoBundle\Event\Listener;

use Doctrine\ORM\EntityRepository;
use OpenLoyalty\Domain\Segment\SystemEvent\CustomerAddedToSegmentSystemEvent;
use OpenLoyalty\Domain\Segment\SystemEvent\CustomerRemovedFromSegmentSystemEvent;
use OpenLoyaltyPlugin\SalesManagoBundle\Service\SalesManagoContactSegmentTagsSender;
use OpenLoyaltyPlugin\SalesManagoBundle\Service\SalesManagoValidator;

/**
 * Class CustomerSerializationListener.
 */
class CustomerSegmentListener
{
    /**
     * @var SalesManagoContactSegmentTagsSender
     */
    protected $sender;
    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * CustomerSegmentListener constructor.
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
     * @param CustomerAddedToSegmentSystemEvent $event
     */
    public function onCustomerAddedToSegment(CustomerAddedToSegmentSystemEvent $event)
    {
        if (SalesManagoValidator::verifySalesManagoEnabled($this->repository)) {
            $this->sender->customerSegmentAdd($event);
        }
    }

    /**
     * @param CustomerRemovedFromSegmentSystemEvent $event
     */
    public function onCustomerRemovedFromSegment(CustomerRemovedFromSegmentSystemEvent $event)
    {
        if (SalesManagoValidator::verifySalesManagoEnabled($this->repository)) {
            $this->sender->customerSegmentRemove($event);
        }
    }
}
