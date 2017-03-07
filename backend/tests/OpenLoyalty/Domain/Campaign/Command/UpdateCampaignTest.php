<?php

namespace OpenLoyalty\Domain\Campaign\Command;

use OpenLoyalty\Domain\Campaign\Campaign;
use OpenLoyalty\Domain\Campaign\CampaignId;
use OpenLoyalty\Domain\Campaign\LevelId;
use OpenLoyalty\Domain\Campaign\Model\Coupon;

/**
 * Class UpdateCampaignTest.
 */
class UpdateCampaignTest extends CampaignCommandHandlerTest
{
    /**
     * @test
     */
    public function it_creates_new_campaign()
    {
        $campaignId = new CampaignId('00000000-0000-0000-0000-000000000000');
        $campaign = new Campaign($campaignId);
        $campaign->setName('not updated');
        $this->campaigns[] = $campaign;

        $handler = $this->createCommandHandler();

        $command = new UpdateCampaign($campaignId, [
            'name' => 'test',
            'reward' => Campaign::REWARD_TYPE_GIFT_CODE,
            'levels' => [new LevelId('00000000-0000-0000-0000-000000000000')],
            'segments' => [],
            'unlimited' => false,
            'limit' => 10,
            'limitPerUser' => 2,
            'singleCoupon' => false,
            'coupons' => [new Coupon('123')],
            'campaignActivity' => [
                'allTimeActive' => false,
                'activeFrom' => new \DateTime('2016-01-01'),
                'activeTo' => new \DateTime('2016-01-11'),
            ],
            'campaignVisibility' => [
                'allTimeVisible' => false,
                'visibleFrom' => new \DateTime('2016-02-01'),
                'visibleTo' => new \DateTime('2016-02-11'),
            ],

        ]);
        $handler->handle($command);
        $campaign = $this->inMemoryRepository->byId($campaignId);
        $this->assertNotNull($campaign);
        $this->assertInstanceOf(Campaign::class, $campaign);
        $this->assertEquals('test', $campaign->getName());
    }
}
