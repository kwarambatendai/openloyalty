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
use OpenLoyalty\Domain\Customer\Event\CustomerWasRegistered;
use OpenLoyalty\Domain\Customer\Event\PosWasAssignedToCustomer;
use OpenLoyalty\Domain\Customer\PosId;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerSystemEvents;

/**
 * Class AssignPosToCustomerTest.
 */
class AssignPosToCustomerTest extends CustomerCommandHandlerTest
{
    /**
     * @test
     */
    public function it_updates_customer_name()
    {
        $customerId    = new CustomerId('00000000-0000-0000-0000-000000000000');
        $posId    = new PosId('00000000-0000-0000-0000-000000000011');
        $this->scenario
            ->withAggregateId($customerId)
            ->given([
                new CustomerWasRegistered($customerId, CustomerCommandHandlerTest::getCustomerData()),
            ])
            ->when(new AssignPosToCustomer($customerId, $posId))
            ->then([
                new PosWasAssignedToCustomer($customerId, $posId)
            ]);
    }

    /**
     * @test
     */
    public function it_dispatch_event_on_update()
    {
        $customerId = new CustomerId('00000000-0000-0000-0000-000000000000');
        $posId  = new PosId('00000000-0000-0000-0000-000000000011');

        $eventStore = new TraceableEventStore(new InMemoryEventStore());

        $messages[] = DomainMessage::recordNow($customerId, 0, new Metadata(array()), new CustomerWasRegistered($customerId, CustomerCommandHandlerTest::getCustomerData()));

        $eventStore->append($customerId, new DomainEventStream($messages));

        $eventBus = new SimpleEventBus();
        $eventDispatcher = $this->getMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo(CustomerSystemEvents::CUSTOMER_UPDATED))
            ->willReturn(true);
        $handler = $this->getCustomerCommandHandler($eventStore, $eventBus, $eventDispatcher);
        $handler->handle(new AssignPosToCustomer($customerId, $posId));
    }
}
