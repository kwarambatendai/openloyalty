<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\DemoBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenLoyalty\Bundle\CampaignBundle\Model\Campaign;
use OpenLoyalty\Bundle\CampaignBundle\Model\CampaignActivity;
use OpenLoyalty\Bundle\CampaignBundle\Model\CampaignVisibility;
use OpenLoyalty\Domain\Campaign\CampaignId;
use OpenLoyalty\Domain\Campaign\Command\CreateCampaign;
use OpenLoyalty\Domain\Campaign\LevelId;
use OpenLoyalty\Domain\Campaign\Model\Coupon;
use OpenLoyalty\Domain\Campaign\SegmentId;
use Symfony\Bridge\Doctrine\Tests\Fixtures\ContainerAwareFixture;

/**
 * Class LoadCampaignData.
 */
class LoadCampaignData extends ContainerAwareFixture implements OrderedFixtureInterface
{
    const CAMPAIGN_ID = '000096cf-32a3-43bd-9034-4df343e5fd93';
    const CAMPAIGN2_ID = '000096cf-32a3-43bd-9034-4df343e5fd92';
    const BIRTHDAY_CAMPAIGN_ID = '000096cf-32a3-43bd-9034-4df343e5fd94';
    const VALUE_CODE_CAMPAIGN_ID = '000096cf-32a3-43bd-9034-4df343e5fd96';
    const FREE_DELIVERY_CAMPAIGN_ID = '000096cf-32a3-43bd-9034-4df343e5fd98';
    const DISCOUNT_CODE_CAMPAIGN_ID = '000096cf-32a3-43bd-9034-4df343e5fd88';
    const EVENT_CODE_CAMPAIGN_ID = '000096cf-32a3-43bd-9034-4df343e5fd81';
    const GOLD_LEVEL_CAMPAIGN_ID = '000096cf-32a3-43bd-9034-4df343e5fd82';
    const NEW_MEMBERS_CAMPAIGN_ID = '000096cf-32a3-43bd-9034-4df343e5fd83';
    const REGISTRATION_CAMPAIGN_ID = '000096cf-32a3-43bd-9034-4df343e5fd84';
    const VIP_CAMPAIGN_ID = '000096cf-32a3-43bd-9034-4df343e5fd85';
    const FIRST_PURCHASE_CAMPAIGN_ID = '000096cf-32a3-43bd-9034-4df343e5fd86';
    const SECOND_PRODUCT_CAMPAIGN_ID = '000096cf-32a3-43bd-9034-4df343e5fd87';
    const SECOND_PURCHASE_CAMPAIGN_ID = '000096cf-32a3-43bd-9034-4df343e5fd78';

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->container->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateCampaign(
                    new CampaignId(self::BIRTHDAY_CAMPAIGN_ID),
                    $this->getBirthdayCampaignData()->toArray()
                )
            );

        $this->container->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateCampaign(
                    new CampaignId(self::VALUE_CODE_CAMPAIGN_ID),
                    $this->getValueCodeCampaignData()->toArray()
                )
            );
        $this->container->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateCampaign(
                    new CampaignId(self::FREE_DELIVERY_CAMPAIGN_ID),
                    $this->getFreeDeliveryCampaignData()->toArray()
                )
            );
        $this->container->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateCampaign(
                    new CampaignId(self::DISCOUNT_CODE_CAMPAIGN_ID),
                    $this->getDiscountCodeCampaignData()->toArray()
                )
            );
        $this->container->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateCampaign(
                    new CampaignId(self::EVENT_CODE_CAMPAIGN_ID),
                    $this->getEventCampaignData()->toArray()
                )
            );

        $this->container->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateCampaign(
                    new CampaignId(self::GOLD_LEVEL_CAMPAIGN_ID),
                    $this->getGoldLevelCampaignData()->toArray()
                )
            );
        $this->container->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateCampaign(
                    new CampaignId(self::NEW_MEMBERS_CAMPAIGN_ID),
                    $this->getNewMembersCampaignData()->toArray()
                )
            );
        $this->container->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateCampaign(
                    new CampaignId(self::REGISTRATION_CAMPAIGN_ID),
                    $this->getRegistrationCampaignData()->toArray()
                )
            );
        $this->container->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateCampaign(
                    new CampaignId(self::VIP_CAMPAIGN_ID),
                    $this->getVIPCampaignData()->toArray()
                )
            );
        $this->container->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateCampaign(
                    new CampaignId(self::FIRST_PURCHASE_CAMPAIGN_ID),
                    $this->getFirstPurchasCampaignData()->toArray()
                )
            );
        $this->container->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateCampaign(
                    new CampaignId(self::SECOND_PRODUCT_CAMPAIGN_ID),
                    $this->getSecondProductCampaignData()->toArray()
                )
            );
        $this->container->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateCampaign(
                    new CampaignId(self::SECOND_PURCHASE_CAMPAIGN_ID),
                    $this->getSecondPurchaseCampaignData()->toArray()
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }

    protected function getBirthdayCampaignData()
    {
        $campaign = new Campaign();
        $campaign->setActive(true);
        $campaign->setCostInPoints(5);
        $campaign->setUnlimited(true);
        $campaign->setSegments([new SegmentId(LoadSegmentData::SEGMENT2_ID)]);
        $campaign->setCoupons(
            [
                new Coupon('1'),
                new Coupon('2'),
                new Coupon('3'),
                new Coupon('4'),
                new Coupon('5'),
            ]
        );
        $campaign->setReward(Campaign::REWARD_TYPE_GIFT_CODE);
        $campaign->setName('Gift for birthday anniversary');
        $campaign->setShortDescription('Gift reward');
        $campaignActivity = new CampaignActivity();
        $campaignActivity->setAllTimeActive(true);
        $campaign->setCampaignActivity($campaignActivity);
        $campaignVisibility = new CampaignVisibility();
        $campaignVisibility->setAllTimeVisible(true);
        $campaign->setCampaignVisibility($campaignVisibility);

        return $campaign;
    }

    protected function getFreeDeliveryCampaignData()
    {
        $campaign = new Campaign();
        $campaign->setActive(true);
        $campaign->setCostInPoints(20);
        $campaign->setUnlimited(false);
        $campaign->setLimit(10);
        $campaign->setLimitPerUser(10);
        $campaign->setLevels(
            [
                new LevelId(LoadLevelData::LEVEL2_ID),
                new LevelId(LoadLevelData::LEVEL_ID),
                new LevelId(LoadLevelData::LEVEL3_ID),
                new LevelId(LoadLevelData::LEVEL4_ID),
            ]
        );

        $campaign->setCoupons(
            [
                new Coupon('1'),
                new Coupon('2'),
                new Coupon('3'),
                new Coupon('4'),
                new Coupon('5'),
                new Coupon('6'),
                new Coupon('7'),
                new Coupon('8'),
                new Coupon('9'),
                new Coupon('10'),
            ]
        );
        $campaign->setReward(Campaign::REWARD_TYPE_FREE_DELIVERY_CODE);
        $campaign->setName('Free delivery');
        $campaign->setConditionsDescription('Terms and conditions of reward');
        $campaign->setUsageInstruction('Instructions how to use coupon');
        $campaign->setShortDescription('Sample free delivery reward');
        $campaignActivity = new CampaignActivity();
        $campaignActivity->setAllTimeActive(true);
        $campaign->setCampaignActivity($campaignActivity);
        $campaignVisibility = new CampaignVisibility();
        $campaignVisibility->setAllTimeVisible(true);
        $campaign->setCampaignVisibility($campaignVisibility);

        return $campaign;
    }

    protected function getDiscountCodeCampaignData()
    {
        $campaign = new Campaign();
        $campaign->setActive(true);
        $campaign->setCostInPoints(20);
        $campaign->setUnlimited(false);
        $campaign->setLimit(10);
        $campaign->setLimitPerUser(1);
        $campaign->setLevels(
            [
                new LevelId(LoadLevelData::LEVEL2_ID),
                new LevelId(LoadLevelData::LEVEL_ID),
                new LevelId(LoadLevelData::LEVEL3_ID),
                new LevelId(LoadLevelData::LEVEL4_ID),
            ]
        );

        $campaign->setCoupons(
            [
                new Coupon('1'),
                new Coupon('2'),
                new Coupon('3'),
                new Coupon('4'),
                new Coupon('5'),
                new Coupon('6'),
                new Coupon('7'),
                new Coupon('8'),
                new Coupon('9'),
                new Coupon('10'),
            ]
        );
        $campaign->setReward(Campaign::REWARD_TYPE_DISCOUNT_CODE);
        $campaign->setName('Discount code reward');
        $campaign->setConditionsDescription('Terms and conditions of reward');
        $campaign->setUsageInstruction('Instructions how to use coupon');
        $campaign->setShortDescription('Sample discount code reward');
        $campaignActivity = new CampaignActivity();
        $campaignActivity->setAllTimeActive(true);
        $campaign->setCampaignActivity($campaignActivity);
        $campaignVisibility = new CampaignVisibility();
        $campaignVisibility->setAllTimeVisible(true);
        $campaign->setCampaignVisibility($campaignVisibility);

        return $campaign;
    }

    protected function getValueCodeCampaignData()
    {
        $campaign = new Campaign();
        $campaign->setActive(true);
        $campaign->setCostInPoints(50);
        $campaign->setUnlimited(false);
        $campaign->setLimit(5);
        $campaign->setLimitPerUser(1);
        $campaign->setLevels(
            [
                new LevelId(LoadLevelData::LEVEL2_ID),
                new LevelId(LoadLevelData::LEVEL_ID),
                new LevelId(LoadLevelData::LEVEL3_ID),
                new LevelId(LoadLevelData::LEVEL4_ID),
            ]
        );

        $campaign->setCoupons(
            [
                new Coupon('1'),
                new Coupon('2'),
                new Coupon('3'),
                new Coupon('4'),
                new Coupon('5'),
            ]
        );
        $campaign->setReward(Campaign::REWARD_TYPE_VALUE_CODE);
        $campaign->setName('50 EUR coupon to use in off-line store');
        $campaign->setConditionsDescription('Terms and conditions of reward');
        $campaign->setUsageInstruction('Instructions how to use coupon');
        $campaign->setShortDescription('Value code reward');
        $campaignActivity = new CampaignActivity();
        $campaignActivity->setAllTimeActive(true);
        $campaign->setCampaignActivity($campaignActivity);
        $campaignVisibility = new CampaignVisibility();
        $campaignVisibility->setAllTimeVisible(true);
        $campaign->setCampaignVisibility($campaignVisibility);

        return $campaign;
    }

    protected function getEventCampaignData()
    {
        $campaign = new Campaign();
        $campaign->setActive(true);
        $campaign->setCostInPoints(100);
        $campaign->setUnlimited(false);
        $campaign->setLimit(5);
        $campaign->setLimitPerUser(1);
        $campaign->setLevels(
            [
                new LevelId(LoadLevelData::LEVEL2_ID),
                new LevelId(LoadLevelData::LEVEL_ID),
                new LevelId(LoadLevelData::LEVEL3_ID),
                new LevelId(LoadLevelData::LEVEL4_ID),
            ]
        );

        $campaign->setCoupons(
            [
                new Coupon('1'),
                new Coupon('2'),
                new Coupon('3'),
                new Coupon('4'),
                new Coupon('5'),
            ]
        );
        $campaign->setReward(Campaign::REWARD_TYPE_EVENT_CODE);
        $campaign->setName('Invitation for the event');
        $campaign->setConditionsDescription('Terms and conditions of reward');
        $campaign->setUsageInstruction('Instructions how to use coupon');
        $campaign->setShortDescription('Sample invitation reward');
        $campaignActivity = new CampaignActivity();
        $campaignActivity->setAllTimeActive(true);
        $campaign->setCampaignActivity($campaignActivity);
        $campaignVisibility = new CampaignVisibility();
        $campaignVisibility->setAllTimeVisible(true);
        $campaign->setCampaignVisibility($campaignVisibility);

        return $campaign;
    }

    protected function getNewMembersCampaignData()
    {
        $campaign = new Campaign();
        $campaign->setActive(true);
        $campaign->setCostInPoints(5);
        $campaign->setUnlimited(false);
        $campaign->setLimit(5);
        $campaign->setLimitPerUser(1);
        $campaign->setLevels(
            [
                new LevelId(LoadLevelData::LEVEL2_ID),
                new LevelId(LoadLevelData::LEVEL_ID),
                new LevelId(LoadLevelData::LEVEL3_ID),
                new LevelId(LoadLevelData::LEVEL4_ID),
            ]
        );

        $campaign->setCoupons(
            [
                new Coupon('1'),
                new Coupon('2'),
                new Coupon('3'),
                new Coupon('4'),
                new Coupon('5'),
            ]
        );
        $campaign->setReward(Campaign::REWARD_TYPE_GIFT_CODE);
        $campaign->setName('Gift for new members');
        $campaign->setShortDescription('Gift reward');
        $campaign->setConditionsDescription('Terms and conditions of reward');
        $campaign->setUsageInstruction('Instructions how to use coupon');
        $campaignActivity = new CampaignActivity();
        $campaignActivity->setAllTimeActive(true);
        $campaign->setCampaignActivity($campaignActivity);
        $campaignVisibility = new CampaignVisibility();
        $campaignVisibility->setAllTimeVisible(true);
        $campaign->setCampaignVisibility($campaignVisibility);

        return $campaign;
    }

    protected function getRegistrationCampaignData()
    {
        $campaign = new Campaign();
        $campaign->setActive(true);
        $campaign->setCostInPoints(0);
        $campaign->setUnlimited(true);
        $campaign->setSegments([new SegmentId(LoadSegmentData::SEGMENT2_ID)]);
        $campaign->setCoupons(
            [
                new Coupon('1'),
                new Coupon('2'),
                new Coupon('3'),
                new Coupon('4'),
                new Coupon('5'),
            ]
        );
        $campaign->setReward(Campaign::REWARD_TYPE_FREE_DELIVERY_CODE);
        $campaign->setName('Gift for registration anniversary');
        $campaign->setShortDescription('Gift reward');
        $campaign->setConditionsDescription('Terms and conditions of reward');
        $campaign->setUsageInstruction('Instructions how to use coupon');
        $campaignActivity = new CampaignActivity();
        $campaignActivity->setAllTimeActive(true);
        $campaign->setCampaignActivity($campaignActivity);
        $campaignVisibility = new CampaignVisibility();
        $campaignVisibility->setAllTimeVisible(true);
        $campaign->setCampaignVisibility($campaignVisibility);

        return $campaign;
    }

    protected function getGoldLevelCampaignData()
    {
        $campaign = new Campaign();
        $campaign->setActive(true);
        $campaign->setCostInPoints(300);
        $campaign->setUnlimited(false);
        $campaign->setLimit(5);
        $campaign->setLimitPerUser(1);
        $campaign->setLevels(
            [
                new LevelId(LoadLevelData::LEVEL2_ID),
            ]
        );

        $campaign->setCoupons(
            [
                new Coupon('1'),
                new Coupon('2'),
                new Coupon('3'),
                new Coupon('4'),
                new Coupon('5'),
            ]
        );
        $campaign->setReward(Campaign::REWARD_TYPE_GIFT_CODE);
        $campaign->setName('Exclusive gift for Gold level only');
        $campaign->setShortDescription('Gift reward');
        $campaign->setConditionsDescription('Terms and conditions of reward');
        $campaign->setUsageInstruction('Instructions how to use coupon');
        $campaignActivity = new CampaignActivity();
        $campaignActivity->setAllTimeActive(true);
        $campaign->setCampaignActivity($campaignActivity);
        $campaignVisibility = new CampaignVisibility();
        $campaignVisibility->setAllTimeVisible(true);
        $campaign->setCampaignVisibility($campaignVisibility);

        return $campaign;
    }

    protected function getFirstPurchasCampaignData()
    {
        $campaign = new Campaign();
        $campaign->setActive(true);
        $campaign->setCostInPoints(10);
        $campaign->setUnlimited(false);
        $campaign->setLimit(5);
        $campaign->setLimitPerUser(1);
        $campaign->setSegments([new SegmentId(LoadSegmentData::SEGMENT_MANY_ORDERS)]);

        $campaign->setCoupons(
            [
                new Coupon('1'),
                new Coupon('2'),
                new Coupon('3'),
                new Coupon('4'),
                new Coupon('5'),
            ]
        );
        $campaign->setReward(Campaign::REWARD_TYPE_GIFT_CODE);
        $campaign->setName('Reward for first purchase');
        $campaign->setShortDescription('Gift reward');
        $campaign->setConditionsDescription('Terms and conditions of reward');
        $campaign->setUsageInstruction('Instructions how to use coupon');
        $campaignActivity = new CampaignActivity();
        $campaignActivity->setAllTimeActive(true);
        $campaign->setCampaignActivity($campaignActivity);
        $campaignVisibility = new CampaignVisibility();
        $campaignVisibility->setAllTimeVisible(true);
        $campaign->setCampaignVisibility($campaignVisibility);

        return $campaign;
    }

    protected function getVIPCampaignData()
    {
        $campaign = new Campaign();
        $campaign->setActive(true);
        $campaign->setCostInPoints(0);
        $campaign->setUnlimited(false);
        $campaign->setLimit(10);
        $campaign->setLimitPerUser(1);
        $campaign->setLevels(
            [
                new LevelId(LoadLevelData::LEVEL_ID),
            ]
        );

        $campaign->setCoupons(
            [
                new Coupon('1'),
                new Coupon('2'),
                new Coupon('3'),
                new Coupon('4'),
                new Coupon('5'),
                new Coupon('6'),
                new Coupon('7'),
                new Coupon('8'),
                new Coupon('9'),
                new Coupon('10'),
            ]
        );
        $campaign->setReward(Campaign::REWARD_TYPE_EVENT_CODE);
        $campaign->setName('Exclusive sale for top customers');
        $campaign->setShortDescription('Invitation for the event reward');
        $campaign->setConditionsDescription('Terms and conditions of reward');
        $campaign->setUsageInstruction('Instructions how to use coupon');
        $campaignActivity = new CampaignActivity();
        $campaignActivity->setAllTimeActive(true);
        $campaign->setCampaignActivity($campaignActivity);
        $campaignVisibility = new CampaignVisibility();
        $campaignVisibility->setAllTimeVisible(true);
        $campaign->setCampaignVisibility($campaignVisibility);

        return $campaign;
    }

    protected function getSecondPurchaseCampaignData()
    {
        $campaign = new Campaign();
        $campaign->setActive(true);
        $campaign->setCostInPoints(15);
        $campaign->setUnlimited(false);
        $campaign->setLimit(5);
        $campaign->setLimitPerUser(1);
        $campaign->setSegments([new SegmentId(LoadSegmentData::SEGMENT_ONE_TIMERS)]);

        $campaign->setCoupons(
            [
                new Coupon('1'),
                new Coupon('2'),
                new Coupon('3'),
                new Coupon('4'),
                new Coupon('5'),
            ]
        );
        $campaign->setReward(Campaign::REWARD_TYPE_DISCOUNT_CODE);
        $campaign->setName('15% off for second purchase');
        $campaign->setShortDescription('Discount code');
        $campaign->setConditionsDescription('Terms and conditions of reward');
        $campaign->setUsageInstruction('Instructions how to use coupon');
        $campaignActivity = new CampaignActivity();
        $campaignActivity->setAllTimeActive(true);
        $campaign->setCampaignActivity($campaignActivity);
        $campaignVisibility = new CampaignVisibility();
        $campaignVisibility->setAllTimeVisible(true);
        $campaign->setCampaignVisibility($campaignVisibility);

        return $campaign;
    }

    protected function getSecondProductCampaignData()
    {
        $campaign = new Campaign();
        $campaign->setActive(true);
        $campaign->setCostInPoints(50);
        $campaign->setUnlimited(false);
        $campaign->setLimit(10);
        $campaign->setLimitPerUser(2);
        $campaign->setLevels(
            [
                new LevelId(LoadLevelData::LEVEL2_ID),
                new LevelId(LoadLevelData::LEVEL_ID),
                new LevelId(LoadLevelData::LEVEL3_ID),
                new LevelId(LoadLevelData::LEVEL4_ID),
            ]
        );

        $campaign->setCoupons(
            [
                new Coupon('1'),
                new Coupon('2'),
                new Coupon('3'),
                new Coupon('4'),
                new Coupon('5'),
                new Coupon('6'),
                new Coupon('7'),
                new Coupon('8'),
                new Coupon('9'),
                new Coupon('10'),
            ]
        );
        $campaign->setReward(Campaign::REWARD_TYPE_DISCOUNT_CODE);
        $campaign->setName('Second product for 1 EUR');
        $campaign->setShortDescription('Discount code reward');
        $campaign->setConditionsDescription('Terms and conditions of reward');
        $campaign->setUsageInstruction('Instructions how to use coupon');
        $campaignActivity = new CampaignActivity();
        $campaignActivity->setAllTimeActive(true);
        $campaign->setCampaignActivity($campaignActivity);
        $campaignVisibility = new CampaignVisibility();
        $campaignVisibility->setAllTimeVisible(true);
        $campaign->setCampaignVisibility($campaignVisibility);

        return $campaign;
    }
}
