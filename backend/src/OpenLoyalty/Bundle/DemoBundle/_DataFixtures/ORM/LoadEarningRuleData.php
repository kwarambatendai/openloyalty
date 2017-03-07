<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\DemoBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenLoyalty\Domain\EarningRule\Command\CreateEarningRule;
use OpenLoyalty\Domain\EarningRule\EarningRule;
use OpenLoyalty\Domain\EarningRule\EarningRuleId;
use Symfony\Bridge\Doctrine\Tests\Fixtures\ContainerAwareFixture;

/**
 * Class LoadEarningRuleData.
 */
class LoadEarningRuleData extends ContainerAwareFixture implements FixtureInterface, OrderedFixtureInterface
{
    const EVENT_RULE_ID = '00000000-0000-474c-b092-b0dd880c07e3';
    const EVENT_RULE_ID2 = '7efb80b7-2b34-4edd-b3a2-c4a4cae65df5';
    const EVENT_RULE_ID3 = 'f93e8f4b-24ba-4526-832d-f3d9ff3fc336';
    const EVENT_RULE_ID6 = '291c96ac-7fcd-4422-8962-b48b653db34b';
    const EVENT_RULE_ID7 = '245f5a71-7906-4881-9c43-8e024a3a6bf8';
    const POINT_RULE_ID = '00000000-0000-474c-b092-b0dd880c07e4';
    const POINT_RULE_ID2 = '921ca644-4354-4fc2-93ef-24d235438d5f';
    const PURCHASE_RULE_ID = '00000000-0000-474c-b092-b0dd880c07e2';
    const PURCHASE_RULE_ID2 = 'd1af76dc-b676-4bb2-b0b7-c8a1055df273';

    public function load(ObjectManager $manager)
    {
        $ruleData = array_merge(
            $this->getRegistrationData(),
            [
                'eventName' => 'oloy.account.created',
                'pointsAmount' => 100,
            ]
        );

        $this->container->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateEarningRule(new EarningRuleId(self::EVENT_RULE_ID7), EarningRule::TYPE_EVENT, $ruleData)
            );

        $ruleData = array_merge(
            $this->getPointsMainData(),
            [
                'pointValue' => 1,
                'excludeDeliveryCost' => true,
            ]
        );

        $this->container->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateEarningRule(new EarningRuleId(self::POINT_RULE_ID), EarningRule::TYPE_POINTS, $ruleData)
            );

        $ruleData = array_merge(
            $this->getBlackFridayData(),
            [
                'pointValue' => 1,
                'excludeDeliveryCost' => false,
            ]
        );

        $this->container->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateEarningRule(new EarningRuleId(self::POINT_RULE_ID2), EarningRule::TYPE_POINTS, $ruleData)
            );

        $ruleData = array_merge(
            $this->getMainData(),
            [
                'skuIds' => ['Pmtk000'],
                'pointsAmount' => 500,
            ]
        );

        $this->container->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateEarningRule(
                    new EarningRuleId(self::PURCHASE_RULE_ID),
                    EarningRule::TYPE_PRODUCT_PURCHASE,
                    $ruleData
                )
            );

        $ruleData = array_merge(
            $this->getSpecificProductPromotionData(),
            [
                'skuIds' => ['Pmo000m'],
                'pointsAmount' => 120,
            ]
        );

        $this->container->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateEarningRule(
                    new EarningRuleId(self::PURCHASE_RULE_ID2),
                    EarningRule::TYPE_PRODUCT_PURCHASE,
                    $ruleData
                )
            );
        $ruleData = array_merge(
            $this->getFirstPurchaseData(),
            [
                'eventName' => 'oloy.transaction.customer_first_transaction',
                'pointsAmount' => 50,
            ]
        );

        $this->container->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateEarningRule(new EarningRuleId(self::EVENT_RULE_ID2), EarningRule::TYPE_EVENT, $ruleData)
            );
        $ruleData = array_merge(
            $this->getCustomerLoggedInData(),
            [
                'eventName' => 'oloy.customer.logged_in',
                'pointsAmount' => 5,
            ]
        );

        $this->container->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateEarningRule(new EarningRuleId(self::EVENT_RULE_ID3), EarningRule::TYPE_EVENT, $ruleData)
            );
        $ruleData = array_merge(
            $this->getMultiplierData(),
            [
                'skuIds' => ['msj003xl'],
                'multiplier' => 2,
            ]
        );

        $this->container->get('broadway.command_handling.command_bus')
            ->dispatch(
                new CreateEarningRule(
                    new EarningRuleId(self::EVENT_RULE_ID6),
                    EarningRule::TYPE_MULTIPLY_FOR_PRODUCT,
                    $ruleData
                )
            );
    }

    protected function getMainData()
    {
        return [
            'name' => 'Points for purchasing specific product',
            'description' => 'Customers earn 500 points after purchasing product Pmtk000 (SKU)',
            'startAt' => (new \DateTime('-1 month'))->getTimestamp(),
            'endAt' => (new \DateTime('+1 month'))->getTimestamp(),
            'active' => true,
            'allTimeActive' => false,
        ];
    }

    protected function getPointsMainData()
    {
        return [
            'name' => '1 EUR = 1 point',
            'description' => 'Customers earn 1 point after spending 1 EUR for purchases registered in loyalty program',
            'active' => true,
            'allTimeActive' => true,
        ];
    }

    protected function getBlackFridayData()
    {
        $startDate = \DateTime::createFromFormat('Y-m-d H:i', '2016-12-02 01:00');
        $endDate = \DateTime::createFromFormat('Y-m-d H:i', '2016-12-02 23:00');

        return [
            'name' => 'Additional points for shopping on Black Friday',
            'description' => 'Customers earn 1 additional point after spending 1 EUR for purchases registered in loyalty program during Black Friday',
            'active' => true,
            'allTimeActive' => false,
            'startAt' => $startDate->getTimestamp(),
            'endAt' => $endDate->getTimestamp(),
        ];
    }

    protected function getSpecificProductPromotionData()
    {
        return [
            'name' => 'Points for buying specific product',
            'description' => 'Customers earn 120 points after purchasing product Pmo000m (SKU)',
            'active' => true,
            'allTimeActive' => true,
        ];
    }

    protected function getFirstPurchaseData()
    {
        return [
            'name' => 'Points for first purchase',
            'description' => 'Customers earn 50 points after first purchase registered in loyalty program',
            'active' => true,
            'allTimeActive' => true,
        ];
    }

    protected function getCustomerLoggedInData()
    {
        return [
            'name' => 'Points after check-in',
            'description' => 'Customers earn 5 points after logging in to the loyalty program client cockpit',
            'active' => true,
            'allTimeActive' => true,
        ];
    }

    protected function getMultiplierData()
    {
        return [
            'name' => 'Multiplied points for purchasing specific product',
            'description' => 'Customers earn 2x points after purchasing product msj003xl (SKU)',
            'active' => true,
            'allTimeActive' => true,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }

    protected function getRegistrationData()
    {
        return [
            'name' => 'Points after registration',
            'description' => 'Customers earn 100 points after registration to loyalty program',
            'active' => true,
            'allTimeActive' => true,
        ];
    }
}
