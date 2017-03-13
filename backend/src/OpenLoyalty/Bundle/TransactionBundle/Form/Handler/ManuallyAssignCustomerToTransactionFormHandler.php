<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\TransactionBundle\Form\Handler;

use Broadway\CommandHandling\CommandBusInterface;
use Broadway\EventDispatcher\EventDispatcher;
use OpenLoyalty\Bundle\TransactionBundle\Model\AssignCustomer;
use OpenLoyalty\Domain\Customer\Exception\ToManyResultsException;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use OpenLoyalty\Domain\Transaction\Command\AssignCustomerToTransaction;
use OpenLoyalty\Domain\Transaction\CustomerId;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetails;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetailsRepository;
use OpenLoyalty\Domain\Transaction\SystemEvent\CustomerAssignedToTransactionSystemEvent;
use OpenLoyalty\Domain\Transaction\SystemEvent\TransactionSystemEvents;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class ManuallyAssignCustomerToTransactionFormHandler.
 */
class ManuallyAssignCustomerToTransactionFormHandler
{
    /**
     * @var TransactionDetailsRepository
     */
    protected $transactionDetailsRepository;

    /**
     * @var CustomerDetailsRepository
     */
    protected $customerDetailsRepository;

    /**
     * @var CommandBusInterface
     */
    protected $commandBus;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var AuthorizationChecker
     */
    protected $ac;

    /**
     * ManuallyAssignCustomerToTransactionFormHandler constructor.
     *
     * @param TransactionDetailsRepository $transactionDetailsRepository
     * @param CustomerDetailsRepository    $customerDetailsRepository
     * @param CommandBusInterface          $commandBus
     * @param EventDispatcher              $eventDispatcher
     * @param AuthorizationChecker         $ac
     */
    public function __construct(
        TransactionDetailsRepository $transactionDetailsRepository,
        CustomerDetailsRepository $customerDetailsRepository,
        CommandBusInterface $commandBus,
        EventDispatcher $eventDispatcher,
        AuthorizationChecker $ac
    ) {
        $this->transactionDetailsRepository = $transactionDetailsRepository;
        $this->customerDetailsRepository = $customerDetailsRepository;
        $this->commandBus = $commandBus;
        $this->eventDispatcher = $eventDispatcher;
        $this->ac = $ac;
    }

    /**
     * @param FormInterface $form
     *
     * @return bool|\OpenLoyalty\Domain\Transaction\TransactionId
     */
    public function onSuccess(FormInterface $form)
    {
        /** @var AssignCustomer $data */
        $data = $form->getData();

        $documentNumber = $data->getTransactionDocumentNumber();

        $transactions = $this->transactionDetailsRepository->findBy(['documentNumber' => $documentNumber]);
        if (count($transactions) == 0) {
            $form->get('transactionDocumentNumber')->addError(new FormError('No such transaction'));

            return false;
        }
        /** @var TransactionDetails $transaction */
        $transaction = reset($transactions);

        if (!$this->ac->isGranted('ASSIGN_CUSTOMER_TO_TRANSACTION', $transaction)) {
            throw new AccessDeniedException();
        }

        if ($transaction->getCustomerId()) {
            $form->get('transactionDocumentNumber')->addError(new FormError('Customer is already assign to this transaction'));

            return false;
        }
        $criteria = null;
        $criteria = [];

        $field = 'loyaltyCardNumber';

        if ($data->getCustomerId()) {
            $criteria['id'] = $data->getCustomerId();
            $field = 'customerId';
        }
        if ($data->getCustomerLoyaltyCardNumber()) {
            $criteria['loyaltyCardNumber'] = strtolower($data->getCustomerLoyaltyCardNumber());
        }
        if ($data->getCustomerPhoneNumber()) {
            $criteria['phone'] = strtolower($data->getCustomerPhoneNumber());
            $field = 'customerPhoneNumber';
        }

        if (count($criteria) == 0) {
            throw new \InvalidArgumentException('One customer field is required');
        }

        try {
            $customers = $this->customerDetailsRepository->findOneByCriteria($criteria, 1);
        } catch (ToManyResultsException $e) {
            $form->get($field)->addError(new FormError('To many customers with such data. Please provide more details.'));

            return false;
        }

        $customer = reset($customers);
        if (!$customer instanceof CustomerDetails) {
            $form->get($field)->addError(new FormError('Such customer does not exist. Please provide more details.'));

            return false;
        }

        $this->commandBus->dispatch(
            new AssignCustomerToTransaction(
                $transaction->getTransactionId(),
                new CustomerId($customer->getCustomerId()->__toString())
            )
        );

        $this->eventDispatcher->dispatch(
            TransactionSystemEvents::CUSTOMER_ASSIGNED_TO_TRANSACTION,
            [new CustomerAssignedToTransactionSystemEvent(
                $transaction->getTransactionId(),
                new CustomerId($customer->getCustomerId()->__toString()),
                $transaction->getGrossValue(),
                $transaction->getGrossValueWithoutDeliveryCosts()
            )]
        );

        return $transaction->getTransactionId();
    }
}
