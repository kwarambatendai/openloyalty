<?php

namespace OpenLoyalty\Bundle\UserBundle\Security\Voter;

use OpenLoyalty\Bundle\BaseVoterTest;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Seller\ReadModel\SellerDetailsRepository;

/**
 * Class CustomerVoterTest.
 */
class CustomerVoterTest extends BaseVoterTest
{
    const CUSTOMER_ID = '00000000-0000-474c-b092-b0dd880c0700';
    const CUSTOMER2_ID = '00000000-0000-474c-b092-b0dd880c0701';
    const POS_ID = '00000000-0000-474c-b092-b0dd880c0711';
    const POS2_ID = '00000000-0000-474c-b092-b0dd880c1711';

    /**
     * @test
     */
    public function it_works()
    {
        $attributes = [
            CustomerVoter::CREATE_CUSTOMER => ['seller' => true, 'customer' => false, 'admin' => true],
            CustomerVoter::LIST_CUSTOMERS => ['seller' => true, 'customer' => false, 'admin' => true],
            CustomerVoter::ASSIGN_POS => ['seller' => true, 'customer' => false, 'admin' => true, 'id' => self::CUSTOMER_ID],
            CustomerVoter::ADD_TO_LEVEL => ['seller' => true, 'customer' => false, 'admin' => true, 'id' => self::CUSTOMER_ID],
            CustomerVoter::DEACTIVATE => ['seller' => true, 'customer' => false, 'admin' => true, 'id' => self::CUSTOMER_ID],
            CustomerVoter::VIEW => ['seller' => true, 'customer' => false, 'admin' => true, 'id' => self::CUSTOMER_ID],
            CustomerVoter::VIEW_STATUS => ['seller' => true, 'customer' => false, 'admin' => true, 'id' => self::CUSTOMER_ID],
            CustomerVoter::EDIT => ['seller' => true, 'customer' => false, 'admin' => true, 'id' => self::CUSTOMER_ID],
        ];

        $repo = $this->getMockBuilder(SellerDetailsRepository::class)->getMock();
        $repo->method('find')->willReturn(null);

        $voter = new CustomerVoter($repo);

        $this->makeAssertions($attributes, $voter);
    }

    protected function getSubjectById($id)
    {
        $customer = $this->getMockBuilder(CustomerDetails::class)->disableOriginalConstructor()->getMock();
        $customer->method('getCustomerId')->willReturn(new CustomerId($id));

        return $customer;
    }
}
