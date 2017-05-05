<?php

namespace OpenLoyalty\Domain\Seller\Command;

use Broadway\CommandHandling\Testing\CommandHandlerScenarioTestCase;
use Broadway\EventHandling\EventBusInterface;
use Broadway\EventStore\EventStoreInterface;
use OpenLoyalty\Domain\Seller\SellerRepository;
use OpenLoyalty\Domain\Seller\Validator\SellerUniqueValidator;

/**
 * Class SellerCommandHandlerTest.
 */
abstract class SellerCommandHandlerTest extends CommandHandlerScenarioTestCase
{
    protected function createCommandHandler(EventStoreInterface $eventStore, EventBusInterface $eventBus)
    {
        $sellerDetailsRepository = $this->getMockBuilder('Broadway\ReadModel\RepositoryInterface')->getMock();
        $sellerDetailsRepository->method('findBy')->willReturn([]);
        $validator = new SellerUniqueValidator($sellerDetailsRepository);

        return new SellerCommandHandler(new SellerRepository($eventStore, $eventBus), $validator);
    }
}
