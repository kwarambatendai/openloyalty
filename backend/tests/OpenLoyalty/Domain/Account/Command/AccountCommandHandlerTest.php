<?php

namespace OpenLoyalty\Domain\Account\Command;

use Broadway\CommandHandling\CommandHandlerInterface;
use Broadway\CommandHandling\Testing\CommandHandlerScenarioTestCase;
use Broadway\EventHandling\EventBusInterface;
use Broadway\EventStore\EventStoreInterface;
use OpenLoyalty\Domain\Account\AccountRepository;

/**
 * Class AccountCommandHandlerTest.
 */
abstract class AccountCommandHandlerTest extends CommandHandlerScenarioTestCase
{
    /**
     * Create a command handler for the given scenario test case.
     *
     * @param EventStoreInterface $eventStore
     * @param EventBusInterface $eventBus
     *
     * @return CommandHandlerInterface
     */
    protected function createCommandHandler(EventStoreInterface $eventStore, EventBusInterface $eventBus)
    {
        return new AccountCommandHandler(
            new AccountRepository($eventStore, $eventBus)
        );
    }
}
