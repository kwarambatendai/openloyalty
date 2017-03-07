<?php

namespace OpenLoyalty\Domain\Customer\Command;

use OpenLoyalty\Domain\Customer\CampaignId;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\Event\CampaignWasBoughtByCustomer;
use OpenLoyalty\Domain\Customer\Event\CustomerWasRegistered;
use OpenLoyalty\Domain\Customer\Model\Coupon;

/**
 * Class BuyCampaignTest.
 */
class BuyCampaignTest extends CustomerCommandHandlerTest
{
    /**
     * @test
     */
    public function it_buys_campaign()
    {
        $customerId = new CustomerId('00000000-0000-0000-0000-000000000000');
        $campaignId = new CampaignId('00000000-0000-0000-0000-000000000001');

        $this->scenario
            ->withAggregateId($customerId)
            ->given([
                new CustomerWasRegistered($customerId, CustomerCommandHandlerTest::getCustomerData()),
            ])
            ->when(new BuyCampaign($customerId, $campaignId, 'test', 99,  new Coupon('123')))
            ->then([
                new CampaignWasBoughtByCustomer($customerId, $campaignId, 'test', 99, new Coupon('123'))
            ]);
    }
}
