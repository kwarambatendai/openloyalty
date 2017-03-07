<?php

namespace OpenLoyalty\Domain\Campaign\Command;

use OpenLoyalty\Domain\Campaign\Campaign;
use OpenLoyalty\Domain\Campaign\CampaignId;
use OpenLoyalty\Domain\Campaign\LevelId;
use OpenLoyalty\Domain\Campaign\Model\Coupon;

/**
 * Class CreateCampaignTest.
 */
class CreateCampaignTest extends CampaignCommandHandlerTest
{
    /**
     * @test
     */
    public function it_creates_new_campaign()
    {
        $handler = $this->createCommandHandler();
        $campaignId = new CampaignId('00000000-0000-0000-0000-000000000000');

        $command = new CreateCampaign($campaignId, [
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
    }

    /**
     * @test
     * @expectedException \Assert\InvalidArgumentException
     */
    public function it_validates_required_fields()
    {
        $handler = $this->createCommandHandler();
        $campaignId = new CampaignId('00000000-0000-0000-0000-000000000000');

        $command = new CreateCampaign($campaignId, [
            'reward' => Campaign::REWARD_TYPE_GIFT_CODE,
            'unlimited' => false,
            'limit' => 10,
            'limitPerUser' => 2,
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
    }
}
