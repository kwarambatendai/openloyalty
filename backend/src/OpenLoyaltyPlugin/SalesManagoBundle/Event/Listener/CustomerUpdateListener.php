<?php

namespace OpenLoyaltyPlugin\SalesManagoBundle\Event\Listener;

use Doctrine\ORM\EntityRepository;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerUpdatedSystemEvent;
use OpenLoyaltyPlugin\SalesManagoBundle\Service\SalesManagoContactUpdateSender;
use OpenLoyaltyPlugin\SalesManagoBundle\Service\SalesManagoValidator;

/**
 * Class CustomerSerializationListener.
 */
class CustomerUpdateListener
{
    /**
     * @var SalesManagoContactUpdateSender
     */
    protected $sender;
    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * CustomerUpdateListener constructor.
     *
     * @param SalesManagoContactUpdateSender $sender
     * @param EntityRepository               $repository
     */
    public function __construct(SalesManagoContactUpdateSender $sender, EntityRepository $repository)
    {
        $this->sender = $sender;
        $this->repository = $repository;
    }

    /**
     * @param CustomerUpdatedSystemEvent $event
     */
    public function onCustomerUpdated(CustomerUpdatedSystemEvent $event)
    {
        if (SalesManagoValidator::verifySalesManagoEnabled($this->repository)) {
            $this->sender->customerUpdated($event->getCustomerId());
        }
    }
}
