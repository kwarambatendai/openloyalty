<?php

namespace OpenLoyaltyPlugin\SalesManagoBundle\Event\Listener;

use Doctrine\ORM\EntityRepository;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerRegisteredSystemEvent;
use OpenLoyaltyPlugin\SalesManagoBundle\Service\SalesManagoContactUpdateSender;
use OpenLoyaltyPlugin\SalesManagoBundle\Service\SalesManagoValidator;

/**
 * Class CustomerSerializationListener.
 */
class CustomerCreateListener
{
    protected $sender;
    protected $repository;

    /**
     * CustomerCreateListener constructor.
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
     * @param CustomerRegisteredSystemEvent $event
     */
    public function onCustomerCreated(CustomerRegisteredSystemEvent $event)
    {
        if (SalesManagoValidator::verifySalesManagoEnabled($this->repository)) {
            $this->sender->customerCreated($event->getCustomerId());
        }
    }
}
