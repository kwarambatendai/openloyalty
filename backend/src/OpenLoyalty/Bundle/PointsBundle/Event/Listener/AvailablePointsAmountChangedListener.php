<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\PointsBundle\Event\Listener;

use OpenLoyalty\Bundle\UserBundle\Service\EmailProvider;
use OpenLoyalty\Domain\Account\SystemEvent\AvailablePointsAmountChangedSystemEvent;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;

/**
 * Class AvailablePointsAmountChangedListener.
 */
class AvailablePointsAmountChangedListener
{
    /**
     * @var EmailProvider
     */
    protected $emailProvider;
    /**
     * @var CustomerDetailsRepository
     */
    protected $customerRepository;

    /**
     * AvailablePointsAmountChangedListener constructor.
     *
     * @param EmailProvider             $emailProvider
     * @param CustomerDetailsRepository $customerRepository
     */
    public function __construct(EmailProvider $emailProvider, CustomerDetailsRepository $customerRepository)
    {
        $this->emailProvider = $emailProvider;
        $this->customerRepository = $customerRepository;
    }

    public function onChange(AvailablePointsAmountChangedSystemEvent $event)
    {
        $customerId = $event->getCustomerId();
        $customer = $this->customerRepository->find($customerId->__toString());
        if (!$customer instanceof CustomerDetails) {
            return;
        }

        if ($event->getOperationType() === AvailablePointsAmountChangedSystemEvent::OPERATION_TYPE_ADD) {
            $this->emailProvider->addPointsToCustomer($customer, $event->getCurrentAmount(), $event->getAmountChange());
        }
    }
}
