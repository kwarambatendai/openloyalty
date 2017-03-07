<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Infrastructure\Customer\SystemEvent\Listener;

use Broadway\CommandHandling\CommandBusInterface;
use Broadway\UuidGenerator\UuidGeneratorInterface;
use OpenLoyalty\Domain\Account\AccountId;
use OpenLoyalty\Domain\Account\Command\CreateAccount;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerRegisteredSystemEvent;
use OpenLoyalty\Domain\Account\CustomerId as AccountCustomerId;

/**
 * Class CreateAccountListener.
 */
class CreateAccountListener
{
    /**
     * @var CommandBusInterface
     */
    protected $commandBus;

    /**
     * @var UuidGeneratorInterface
     */
    protected $uuidGenerator;

    /**
     * CreateAccountListener constructor.
     *
     * @param CommandBusInterface    $commandBus
     * @param UuidGeneratorInterface $uuidGenerator
     */
    public function __construct(CommandBusInterface $commandBus, UuidGeneratorInterface $uuidGenerator)
    {
        $this->commandBus = $commandBus;
        $this->uuidGenerator = $uuidGenerator;
    }

    public function handleCustomerRegistered(CustomerRegisteredSystemEvent $event)
    {
        $this->commandBus->dispatch(new CreateAccount(
            new AccountId($this->uuidGenerator->generate()),
            new AccountCustomerId($event->getCustomerId()->__toString())
        ));
    }
}
