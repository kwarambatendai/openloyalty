<?php

namespace OpenLoyalty\Domain\Customer\Command;

use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\Event\CustomerWasDeactivated;
use OpenLoyalty\Domain\Customer\Event\CustomerWasRegistered;

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
        $customerId = new CustomerId('00000000-0000-0000-0000-000000000000');
        $this->scenario
            ->withAggregateId($customerId)
            ->given([
                new CustomerWasRegistered($customerId, CustomerCommandHandlerTest::getCustomerData()),
            ])
            ->when(new DeactivateCustomer($customerId))
            ->then([
                new CustomerWasDeactivated($customerId),
            ]);
    }
}
