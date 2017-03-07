<?php

namespace OpenLoyalty\Domain\Customer\Command;

use Broadway\Domain\DomainEventStream;
use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use Broadway\EventDispatcher\EventDispatcherInterface;
use Broadway\EventHandling\SimpleEventBus;
use Broadway\EventStore\InMemoryEventStore;
use Broadway\EventStore\TraceableEventStore;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\Event\CustomerWasDeactivated;
use OpenLoyalty\Domain\Customer\Event\CustomerWasRegistered;
use OpenLoyalty\Domain\Customer\Event\PosWasAssignedToCustomer;
use OpenLoyalty\Domain\Customer\PosId;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerSystemEvents;

/**
 * Class DeactivateCustomerTest.
 */
class DeactivateCustomerTest extends CustomerCommandHandlerTest
{
    /**
     * @test
     */
    public function it_deactivates_customer()
    {
        $customerId    = new CustomerId('00000000-0000-0000-0000-000000000000');
        $this->scenario
            ->withAggregateId($customerId)
            ->given([
                new CustomerWasRegistered($customerId, CustomerCommandHandlerTest::getCustomerData()),
            ])
            ->when(new DeactivateCustomer($customerId))
            ->then([
                new CustomerWasDeactivated($customerId)
            ]);
    }
}
