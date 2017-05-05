<?php

namespace OpenLoyalty\Bundle\CampaignBundle\Security\Voter;

use OpenLoyalty\Bundle\BaseVoterTest;
use OpenLoyalty\Bundle\CampaignBundle\Service\CampaignProvider;
use OpenLoyalty\Domain\Campaign\Campaign;
use OpenLoyalty\Domain\Campaign\CampaignId;

/**
 * Class CampaignVoterTest.
 */
class CampaignVoterTest extends BaseVoterTest
{
    const CAMPAIGN_ID = '00000000-0000-474c-b092-b0dd880c0700';
    const CAMPAIGN2_ID = '00000000-0000-474c-b092-b0dd880c0702';

    /**
     * @test
     */
    public function it_works()
    {
        $provider = $this->getMockBuilder(CampaignProvider::class)->disableOriginalConstructor()->getMock();
        $provider->method('visibleForCustomers')->with($this->isInstanceOf(Campaign::class))
            ->will($this->returnCallback(function (Campaign $campaign) {
                if ($campaign->getCampaignId()->__toString() == self::CAMPAIGN_ID) {
                    return [self::USER_ID];
                }
                if ($campaign->getCampaignId()->__toString() == self::CAMPAIGN2_ID) {
                    return [];
                }
            }));

        $attributes = [
            CampaignVoter::CREATE_CAMPAIGN => ['seller' => false, 'customer' => false, 'admin' => true],
            CampaignVoter::EDIT => ['seller' => false, 'customer' => false, 'admin' => true, 'id' => self::CAMPAIGN_ID],
            CampaignVoter::VIEW => ['seller' => true, 'customer' => false, 'admin' => true, 'id' => self::CAMPAIGN2_ID],
            CampaignVoter::LIST_ALL_CAMPAIGNS => ['seller' => false, 'customer' => false, 'admin' => true],
            CampaignVoter::LIST_CAMPAIGNS_AVAILABLE_FOR_ME => ['seller' => false, 'customer' => true, 'admin' => false],
            CampaignVoter::LIST_CAMPAIGNS_BOUGHT_BY_ME => ['seller' => false, 'customer' => true, 'admin' => false],
            CampaignVoter::BUY => ['seller' => false, 'customer' => true, 'admin' => false, 'id' => self::CAMPAIGN2_ID],
        ];

        $voter = new CampaignVoter($provider);

        $this->makeAssertions($attributes, $voter);

        $this->assertEquals(true, $voter->vote($this->getCustomerToken(), $this->getSubjectById(self::CAMPAIGN_ID), [CampaignVoter::VIEW]));
    }

    protected function getSubjectById($id)
    {
        $campaign = $this->getMockBuilder(Campaign::class)->disableOriginalConstructor()->getMock();
        $campaign->method('getCampaignId')->willReturn(new CampaignId($id));

        return $campaign;
    }
}
