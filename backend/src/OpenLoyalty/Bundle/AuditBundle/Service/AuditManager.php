<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\AuditBundle\Service;

use Broadway\CommandHandling\CommandBusInterface;
use OpenLoyalty\Domain\Audit\Command\CreateAuditLog;
use OpenLoyalty\Domain\Customer\Command\ActivateCustomer;
use OpenLoyalty\Domain\Customer\Command\AssignPosToCustomer;
use OpenLoyalty\Domain\Customer\Command\DeactivateCustomer;
use OpenLoyalty\Domain\Customer\Command\MoveCustomerToLevel;
use OpenLoyalty\Domain\Customer\Command\RegisterCustomer;
use OpenLoyalty\Domain\Customer\Command\UpdateCustomerDetails;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class AuditManager.
 */
class AuditManager implements AuditManagerInterface
{
    const NOT_LOGGED_USERNAME = '<notlogged>';

    const CUSTOMER_ENTITY_TYPE = 'customer';

    /**
     * @var CommandBusInterface
     */
    protected $commandBus;

    /**
     * @var CustomerDetailsRepository
     */
    protected $customerDetailsRepository;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * AuditManager constructor.
     *
     * @param CommandBusInterface       $commandBus
     * @param CustomerDetailsRepository $customerDetailsRepository
     * @param TokenStorageInterface     $tokenStorage
     */
    public function __construct(
        CommandBusInterface $commandBus,
        CustomerDetailsRepository $customerDetailsRepository,
        TokenStorageInterface $tokenStorage
    ) {
        $this->commandBus = $commandBus;
        $this->customerDetailsRepository = $customerDetailsRepository;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param $command
     */
    public function handleCommand($command)
    {
        $eventType = substr(strrchr(get_class($command), '\\'), 1);

        if ($command instanceof UpdateCustomerDetails) {
            $this->auditCustomerEvent($eventType, $command->getCustomerId(), $command->getCustomerData());
        } elseif ($command instanceof RegisterCustomer) {
            $this->auditCustomerEvent($eventType, $command->getCustomerId(), $command->getCustomerData());
        } elseif ($command instanceof ActivateCustomer) {
            $this->auditCustomerEvent($eventType, $command->getCustomerId());
        } elseif ($command instanceof AssignPosToCustomer) {
            $this->auditCustomerEvent($eventType, $command->getCustomerId(), [$command->getPosId()->__toString()]);
        } elseif ($command instanceof DeactivateCustomer) {
            $this->auditCustomerEvent($eventType, $command->getCustomerId());
        } elseif ($command instanceof MoveCustomerToLevel) {
            $this->auditCustomerEvent($eventType, $command->getCustomerId(), [$command->getLevelId()->__toString()]);
        }
    }

    /**
     * @param string     $eventType
     * @param CustomerId $customerId
     * @param array      $data
     */
    public function auditCustomerEvent($eventType, CustomerId $customerId, array $data = [])
    {
        $token = $this->tokenStorage->getToken();

        $command = CreateAuditLog::create(
            $eventType,
            self::CUSTOMER_ENTITY_TYPE,
            $customerId,
            $token ? $token->getUsername() : self::NOT_LOGGED_USERNAME,
            $data
        );
        $this->commandBus->dispatch($command);
    }
}
