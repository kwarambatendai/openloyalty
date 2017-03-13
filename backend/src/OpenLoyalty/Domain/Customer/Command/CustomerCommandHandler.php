<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Customer\Command;

use Broadway\CommandHandling\CommandHandler;
use Broadway\EventDispatcher\EventDispatcherInterface;
use OpenLoyalty\Domain\Customer\Customer;
use OpenLoyalty\Domain\Customer\CustomerRepository;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerActivatedSystemEvent;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerAgreementsUpdatedSystemEvent;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerDeactivatedSystemEvent;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerReferralSystemEvent;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerRegisteredSystemEvent;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerSystemEvents;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerUpdatedSystemEvent;
use OpenLoyalty\Domain\Customer\SystemEvent\NewsletterSubscriptionSystemEvent;
use OpenLoyalty\Domain\Customer\Validator\CustomerUniqueValidator;

/**
 * Class CustomerCommandHandler.
 */
class CustomerCommandHandler extends CommandHandler
{
    /**
     * @var CustomerRepository
     */
    private $repository;

    /**
     * @var CustomerUniqueValidator
     */
    private $customerUniqueValidator;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * CustomerCommandHandler constructor.
     *
     * @param CustomerRepository       $repository
     * @param CustomerUniqueValidator  $customerUniqueValidator
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        CustomerRepository $repository,
        CustomerUniqueValidator $customerUniqueValidator,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->repository = $repository;
        $this->customerUniqueValidator = $customerUniqueValidator;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function handleRegisterCustomer(RegisterCustomer $command)
    {
        $customerData = $command->getCustomerData();
        $this->customerUniqueValidator->validateEmailUnique($customerData['email']);
        if (isset($customerData['phone']) && $customerData['phone']) {
            $this->customerUniqueValidator->validatePhoneUnique($customerData['phone']);
        }
        /** @var Customer $customer */
        $customer = Customer::registerCustomer($command->getCustomerId(), $customerData);
        $this->repository->save($customer);

        $this->eventDispatcher->dispatch(
            CustomerSystemEvents::CUSTOMER_REGISTERED,
            [new CustomerRegisteredSystemEvent($command->getCustomerId())]
        );
    }

    public function handleUpdateCustomerAddress(UpdateCustomerAddress $command)
    {
        $customerId = $command->getCustomerId();
        /** @var Customer $customer */
        $customer = $this->repository->load($customerId);
        $customer->updateAddress($command->getAddressData());
        $this->repository->save($customer);
        $this->eventDispatcher->dispatch(
            CustomerSystemEvents::CUSTOMER_UPDATED,
            [new CustomerUpdatedSystemEvent($customerId)]
        );
    }

    public function handleUpdateCustomerCompanyDetails(UpdateCustomerCompanyDetails $command)
    {
        $customerId = $command->getCustomerId();
        /** @var Customer $customer */
        $customer = $this->repository->load($customerId);
        $customer->updateCompanyDetails($command->getCompanyData());
        $this->repository->save($customer);
        $this->eventDispatcher->dispatch(
            CustomerSystemEvents::CUSTOMER_UPDATED,
            [new CustomerUpdatedSystemEvent($customerId)]
        );
    }

    public function handleUpdateCustomerLoyaltyCardNumber(UpdateCustomerLoyaltyCardNumber $command)
    {
        $customerId = $command->getCustomerId();
        $this->customerUniqueValidator->validateLoyaltyCardNumberUnique($command->getCardNumber(), $customerId);
        /** @var Customer $customer */
        $customer = $this->repository->load($customerId);
        $customer->updateLoyaltyCardNumber($command->getCardNumber());
        $this->repository->save($customer);
        $this->eventDispatcher->dispatch(
            CustomerSystemEvents::CUSTOMER_UPDATED,
            [new CustomerUpdatedSystemEvent($customerId)]
        );
    }

    public function handleUpdateCustomerDetails(UpdateCustomerDetails $command)
    {
        $customerId = $command->getCustomerId();
        $customerData = $command->getCustomerData();
        if (isset($customerData['email'])) {
            $this->customerUniqueValidator->validateEmailUnique($customerData['email'], $customerId);
        }
        if (isset($customerData['phone']) && $customerData['phone']) {
            $this->customerUniqueValidator->validatePhoneUnique($customerData['phone'], $customerId);
        }
        /** @var Customer $customer */
        $customer = $this->repository->load($customerId->__toString());
        $oldAgreements = [
            'agreement1' => $customer->isAgreement1(),
            'agreement2' => $customer->isAgreement2(),
            'agreement3' => $customer->isAgreement3(),
        ];

        $customer->updateCustomerDetails($customerData);
        $this->repository->save($customer);

        $newAgreements = [
            'agreement1' => [
                'new' => $customer->isAgreement1(),
                'old' => $oldAgreements['agreement1'],
            ],
            'agreement2' => [
                'new' => $customer->isAgreement2(),
                'old' => $oldAgreements['agreement2'],
            ],
            'agreement3' => [
                'new' => $customer->isAgreement3(),
                'old' => $oldAgreements['agreement3'],
            ],
        ];

        foreach ($newAgreements as $key => $agr) {
            if ($agr['new'] === $agr['old']) {
                unset($newAgreements[$key]);
            }
        }

        if (count($newAgreements) > 0) {
            $this->eventDispatcher->dispatch(
                CustomerSystemEvents::CUSTOMER_AGREEMENTS_UPDATED,
                [new CustomerAgreementsUpdatedSystemEvent($customerId, $newAgreements)]
            );
        }

        $this->eventDispatcher->dispatch(
            CustomerSystemEvents::CUSTOMER_UPDATED,
            [new CustomerUpdatedSystemEvent($customerId)]
        );
    }

    public function handleMoveCustomerToLevel(MoveCustomerToLevel $command)
    {
        $customerId = $command->getCustomerId();
        /** @var Customer $customer */
        $customer = $this->repository->load($customerId->__toString());
        $customer->addToLevel($command->getLevelId(), $command->isManually());
        $this->repository->save($customer);
        $this->eventDispatcher->dispatch(
            CustomerSystemEvents::CUSTOMER_UPDATED,
            [new CustomerUpdatedSystemEvent($customerId)]
        );
    }

    public function handleAssignPosToCustomer(AssignPosToCustomer $command)
    {
        $customerId = $command->getCustomerId();
        /** @var Customer $customer */
        $customer = $this->repository->load($customerId->__toString());
        $customer->assignPosToCustomer($command->getPosId());
        $this->repository->save($customer);
        $this->eventDispatcher->dispatch(
            CustomerSystemEvents::CUSTOMER_UPDATED,
            [new CustomerUpdatedSystemEvent($customerId)]
        );
    }

    public function handleBuyCampaign(BuyCampaign $command)
    {
        $customerId = $command->getCustomerId();
        /** @var Customer $customer */
        $customer = $this->repository->load($customerId->__toString());
        $customer->buyCampaign($command->getCampaignId(), $command->getCampaignName(), $command->getCostInPoints(), $command->getCoupon());
        $this->repository->save($customer);
    }

    public function handleChangeCampaignUsage(ChangeCampaignUsage $command)
    {
        $customerId = $command->getCustomerId();
        /** @var Customer $customer */
        $customer = $this->repository->load($customerId->__toString());
        $customer->changeCampaignUsage($command->getCampaignId(), $command->getCoupon(), $command->isUsed());
        $this->repository->save($customer);
    }

    public function handleDeactivateCustomer(DeactivateCustomer $command)
    {
        $customerId = $command->getCustomerId();
        /** @var Customer $customer */
        $customer = $this->repository->load($customerId->__toString());
        $customer->deactivate();
        $this->repository->save($customer);
        $this->eventDispatcher->dispatch(
            CustomerSystemEvents::CUSTOMER_DEACTIVATED,
            [new CustomerDeactivatedSystemEvent($customerId)]
        );
    }

    public function handleActivateCustomer(ActivateCustomer $command)
    {
        $customerId = $command->getCustomerId();
        /** @var Customer $customer */
        $customer = $this->repository->load($customerId->__toString());
        $customer->activate();
        $this->repository->save($customer);
        $this->eventDispatcher->dispatch(
            CustomerSystemEvents::CUSTOMER_ACTIVATED,
            [new CustomerActivatedSystemEvent($customerId)]
        );
    }

    public function handleCustomerReferral(CustomerReferral $command)
    {
        $this->eventDispatcher->dispatch(
            CustomerSystemEvents::CUSTOMER_REFERRAL,
            [new CustomerReferralSystemEvent($command->getCustomerId(), $command->getReferralCustomerId())]
        );
    }

    public function handleNewsletterSubscription(NewsletterSubscription $command)
    {
        $this->eventDispatcher->dispatch(
            CustomerSystemEvents::NEWSLETTER_SUBSCRIPTION,
            [new NewsletterSubscriptionSystemEvent($command->getCustomerId())]
        );
    }
}
