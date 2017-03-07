<?php

namespace OpenLoyalty\Bundle\UserBundle\Controller;

use OpenLoyalty\Domain\Customer\Customer;
use OpenLoyalty\Domain\Customer\CustomerId;

/**
 * Class CustomerTest.
 */
class CustomerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_exception_on_empty_first_name()
    {
        $customer = new Customer(new CustomerId('00-000-000-000'));
        $customer->setFirstName('');
    }
}
