<?php

namespace OpenLoyalty\Bundle\UserBundle\EventListener;

use OpenLoyalty\Bundle\UserBundle\Service\EmailProvider;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerLevelChangedSystemEvent;
use OpenLoyalty\Domain\Level\Level;
use OpenLoyalty\Domain\Level\LevelRepository;

/**
 * Class CustomerLevelChangedListener.
 */
class CustomerLevelChangedListener
{
    /**
     * @var EmailProvider
     */
    protected $emailProvider;

    /**
     * @var LevelRepository
     */
    protected $levelRepository;

    /**
     * @var CustomerDetailsRepository
     */
    protected $customerRepository;

    /**
     * OnCustomerLevelChangedListener constructor.
     *
     * @param EmailProvider             $emailProvider
     * @param LevelRepository           $levelRepository
     * @param CustomerDetailsRepository $customerRepository
     */
    public function __construct(
        EmailProvider $emailProvider,
        LevelRepository $levelRepository,
        CustomerDetailsRepository $customerRepository
    ) {
        $this->emailProvider = $emailProvider;
        $this->levelRepository = $levelRepository;
        $this->customerRepository = $customerRepository;
    }

    public function sendEmail(CustomerLevelChangedSystemEvent $event)
    {
        $customerId = $event->getCustomerId();
        $levelId = $event->getLevelId();

        /** @var Level $level */
        $level = $this->levelRepository->byId(new \OpenLoyalty\Domain\Level\LevelId($levelId->__toString()));
        /** @var CustomerDetails $customer */
        $customer = $this->customerRepository->find($customerId->__toString());
        if (!$customer instanceof CustomerDetails || !$level instanceof Level) {
            return;
        }

        $this->emailProvider->moveToLevel($customer, $level);
    }
}
