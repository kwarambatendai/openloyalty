<?php

namespace OpenLoyalty\Domain\Transaction\Command;

use Broadway\CommandHandling\Testing\CommandHandlerScenarioTestCase;
use Broadway\EventHandling\EventBusInterface;
use Broadway\EventStore\EventStoreInterface;
use OpenLoyalty\Domain\Transaction\TransactionRepository;

/**
 * Class TransactionCommandHandlerTest.
 */
abstract class TransactionCommandHandlerTest extends CommandHandlerScenarioTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createCommandHandler(EventStoreInterface $eventStore, EventBusInterface $eventBus)
    {
        return new TransactionCommandHandler(
            new TransactionRepository($eventStore, $eventBus)
        );
    }

}
