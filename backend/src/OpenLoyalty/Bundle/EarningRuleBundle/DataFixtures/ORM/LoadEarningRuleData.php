<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\EarningRuleBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerSystemEvents;
use OpenLoyalty\Domain\EarningRule\Command\CreateEarningRule;
use OpenLoyalty\Domain\EarningRule\EarningRule;
use OpenLoyalty\Domain\EarningRule\EarningRuleId;
use OpenLoyalty\Domain\Model\SKU;
use Symfony\Bridge\Doctrine\Tests\Fixtures\ContainerAwareFixture;
use OpenLoyalty\Bundle\LevelBundle\DataFixtures\ORM\LoadLevelData;
use OpenLoyalty\Domain\EarningRule\LevelId;

/**
 * Class LoadEarningRuleData.
 */
class LoadEarningRuleData extends ContainerAwareFixture implements FixtureInterface, OrderedFixtureInterface
{
    const EVENT_RULE_ID = '00000000-0000-474c-b092-b0dd880c07e3';
    const POINT_RULE_ID = '00000000-0000-474c-b092-b0dd880c07e4';
    const PURCHASE_RULE_ID = '00000000-0000-474c-b092-b0dd880c07e2';
    const MULTIPLY_RULE_ID = '00000000-0000-474c-b092-b0dd880c0723';
    const NEWSLETTER_SUBSCRIPTION_RULE_ID = '00000000-0000-474c-b092-b0dd880c0725';
    const FACEBOOK_LIKE_RULE_ID = '00000000-0000-474c-b092-b0dd880c0121';

    public function load(ObjectManager $manager)
    {
        $ruleData = array_merge($this->getMainData(), [
            'eventName' => 'test event',
            'pointsAmount' => 100,
        ]);

        $this->container->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateEarningRule(new EarningRuleId(self::EVENT_RULE_ID), EarningRule::TYPE_EVENT, $ruleData)
            );

        $ruleData = array_merge($this->getMainData(), [
            'excludedSKUs' => [(new SKU('123'))->serialize(), (new SKU('234'))->serialize(), (new SKU('567'))->serialize()],
            'pointValue' => 2.3,
        ]);

        $this->container->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateEarningRule(new EarningRuleId(self::POINT_RULE_ID), EarningRule::TYPE_POINTS, $ruleData)
            );

        $ruleData = array_merge($this->getMainData(), [
            'skuIds' => ['ssku'],
            'pointsAmount' => 120,
        ]);

        $this->container->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateEarningRule(new EarningRuleId(self::PURCHASE_RULE_ID), EarningRule::TYPE_PRODUCT_PURCHASE, $ruleData)
            );

        $ruleData = array_merge($this->getMainData(), [
            'skuIds' => ['SKU123'],
            'multiplier' => 2,
        ]);

        $this->container->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateEarningRule(new EarningRuleId(self::MULTIPLY_RULE_ID), EarningRule::TYPE_MULTIPLY_FOR_PRODUCT, $ruleData)
            );

        $ruleData = array_merge($this->getMainData(), [
            'eventName' => CustomerSystemEvents::NEWSLETTER_SUBSCRIPTION,
            'pointsAmount' => 85,
        ]);
        $ruleData['name'] = 'Newsletter subscription test rule';

        $ruleData['levels'] = [new LevelId(LoadLevelData::LEVEL3_ID)];

        $this->container->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateEarningRule(new EarningRuleId(self::NEWSLETTER_SUBSCRIPTION_RULE_ID), EarningRule::TYPE_EVENT, $ruleData)
            );

        $ruleData = array_merge($this->getMainData(), [
            'eventName' => 'facebook_like',
            'pointsAmount' => 100,
        ]);
        $ruleData['name'] = 'Facebook like test rule';

        $this->container->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateEarningRule(new EarningRuleId(self::FACEBOOK_LIKE_RULE_ID), EarningRule::TYPE_CUSTOM_EVENT, $ruleData)
            );
    }

    protected function getMainData()
    {
        return [
            'name' => 'test',
            'description' => 'sth',
            'startAt' => (new \DateTime('-1 month'))->getTimestamp(),
            'endAt' => (new \DateTime('+1 month'))->getTimestamp(),
            'active' => true,
            'allTimeActive' => false,
            'levels' => ([new LevelId(LoadLevelData::LEVEL3_ID)]),
        ];
    }

    public function getOrder()
    {
        return 0;
    }
}
