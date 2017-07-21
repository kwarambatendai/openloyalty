<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UserBundle\Form\Handler;

use Broadway\CommandHandling\CommandBusInterface;
use Broadway\UuidGenerator\UuidGeneratorInterface;
use Doctrine\ORM\EntityManager;
use OpenLoyalty\Bundle\UserBundle\Service\UserManager;
use OpenLoyalty\Domain\Customer\Command\MoveCustomerToLevel;
use OpenLoyalty\Domain\Customer\Command\RegisterCustomer;
use OpenLoyalty\Domain\Customer\Command\UpdateCustomerAddress;
use OpenLoyalty\Domain\Customer\Command\UpdateCustomerCompanyDetails;
use OpenLoyalty\Domain\Customer\Command\UpdateCustomerLoyaltyCardNumber;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\Exception\EmailAlreadyExistsException;
use OpenLoyalty\Domain\Customer\Exception\LoyaltyCardNumberAlreadyExistsException;
use OpenLoyalty\Domain\Customer\Exception\PhoneAlreadyExistsException;
use OpenLoyalty\Domain\Customer\LevelId;
use OpenLoyalty\Domain\Customer\Validator\CustomerUniqueValidator;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

/**
 * Class CustomerRegistrationFormHandler.
 */
class CustomerRegistrationFormHandler
{
    /**
     * @var CommandBusInterface
     */
    protected $commandBus;

    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var UuidGeneratorInterface
     */
    protected $uuidGenerator;

    /**
     * @var CustomerUniqueValidator
     */
    protected $customerUniqueValidator;

    /**
     * CustomerRegistrationFormHandler constructor.
     *
     * @param CommandBusInterface     $commandBus
     * @param UserManager             $userManager
     * @param EntityManager           $em
     * @param UuidGeneratorInterface  $uuidGenerator
     * @param CustomerUniqueValidator $customerUniqueValidator
     */
    public function __construct(
        CommandBusInterface $commandBus,
        UserManager $userManager,
        EntityManager $em,
        UuidGeneratorInterface $uuidGenerator,
        CustomerUniqueValidator $customerUniqueValidator
    ) {
        $this->commandBus = $commandBus;
        $this->userManager = $userManager;
        $this->em = $em;
        $this->uuidGenerator = $uuidGenerator;
        $this->customerUniqueValidator = $customerUniqueValidator;
    }

    public function onSuccess(CustomerId $customerId, FormInterface $form)
    {
        $customerData = $form->getData();
        if (!$customerData['company']['name'] && !$customerData['company']['nip']) {
            unset($customerData['company']);
        }
        $password = null;
        if ($form->has('plainPassword')) {
            $password = $customerData['plainPassword'];
            unset($customerData['plainPassword']);
        }

        $command = new RegisterCustomer($customerId, $customerData);

        $email = $customerData['email'];
        $emailExists = false;
        if ($this->userManager->isCustomerExist($email)) {
            $emailExists = 'This email is already taken';
        }
        try {
            $this->customerUniqueValidator->validateEmailUnique($email, $customerId);
        } catch (EmailAlreadyExistsException $e) {
            $emailExists = $e->getMessage();
        }
        if ($emailExists) {
            $form->get('email')->addError(new FormError($emailExists));
        }
        if (isset($customerData['loyaltyCardNumber'])) {
            try {
                $this->customerUniqueValidator->validateLoyaltyCardNumberUnique($customerData['loyaltyCardNumber'], $customerId);
            } catch (LoyaltyCardNumberAlreadyExistsException $e) {
                $form->get('loyaltyCardNumber')->addError(new FormError($e->getMessage()));
            }
        }
        if (isset($customerData['phone']) && $customerData['phone']) {
            try {
                $this->customerUniqueValidator->validatePhoneUnique($customerData['phone']);
            } catch (PhoneAlreadyExistsException $e) {
                $form->get('phone')->addError(new FormError($e->getMessage()));
            }
        }

        if ($form->getErrors(true)->count() > 0) {
            return $form->getErrors();
        }

        $this->commandBus->dispatch($command);
        if (isset($customerData['address'])) {
            $updateAddressCommand = new UpdateCustomerAddress($customerId, $customerData['address']);
            $this->commandBus->dispatch($updateAddressCommand);
        }
        if (isset($customerData['company']) && $customerData['company'] && $customerData['company']['name'] && $customerData['company']['nip']) {
            $updateCompanyDataCommand = new UpdateCustomerCompanyDetails($customerId, $customerData['company']);
            $this->commandBus->dispatch($updateCompanyDataCommand);
        }
        if (isset($customerData['loyaltyCardNumber'])) {
            $loyaltyCardCommand = new UpdateCustomerLoyaltyCardNumber($customerId, $customerData['loyaltyCardNumber']);
            $this->commandBus->dispatch($loyaltyCardCommand);
        }

        if (isset($customerData['level'])) {
            $this->commandBus->dispatch(
                new MoveCustomerToLevel($customerId, new LevelId($customerData['level']), true)
            );
        }

        return $this->userManager->createNewCustomer($customerId, $email, $password);
    }
}
