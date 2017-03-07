<?php

namespace OpenLoyalty\Domain\Customer\Command;

use Broadway\EventDispatcher\EventDispatcherInterface;
use Broadway\EventHandling\SimpleEventBus;
use Broadway\EventStore\InMemoryEventStore;
use Broadway\EventStore\TraceableEventStore;
use OpenLoyalty\Domain\Customer\Event\CustomerWasRegistered;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerSystemEvents;

/**
 * Class RegisterCustomerTest.
 *
 * @package OpenLoyalty\Domain\User\Command
 */
class RegisterCustomerTest extends CustomerCommandHandlerTest
{
    /**
     * @test
     */
    public function it_registers_new_customer()
    {
        $customerId = new CustomerId('00000000-0000-0000-0000-000000000000');
        $this->scenario
            ->withAggregateId($customerId)
            ->given([])
            ->when(new RegisterCustomer($customerId, CustomerCommandHandlerTest::getCustomerData()))
            ->then(array(
                new CustomerWasRegistered($customerId, CustomerCommandHandlerTest::getCustomerData())
            ));
    }

    /**
     * @test
     */
    public function it_dispatch_event_on_register()
    {
        $customerId = new CustomerId('00000000-0000-0000-0000-000000000000');

        $eventStore = new TraceableEventStore(new InMemoryEventStore());

        $eventBus = new SimpleEventBus();
        $eventDispatcher = $this->getMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo(CustomerSystemEvents::CUSTOMER_REGISTERED))
            ->willReturn(true);
        $handler = $this->getCustomerCommandHandler($eventStore, $eventBus, $eventDispatcher);
        $handler->handle(new RegisterCustomer($customerId, CustomerCommandHandlerTest::getCustomerData()));
    }
}
