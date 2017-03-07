<?php

namespace OpenLoyalty\Bundle\DemoBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenLoyalty\Domain\Level\Command\ActivateLevel;
use OpenLoyalty\Domain\Level\Command\CreateLevel;
use OpenLoyalty\Domain\Level\LevelId;
use Symfony\Bridge\Doctrine\Tests\Fixtures\ContainerAwareFixture;

/**
 * Class LoadLevelData.
 */
class LoadLevelData extends ContainerAwareFixture implements OrderedFixtureInterface
{
    const LEVEL_ID = 'f99748f2-bf86-11e6-a4a6-cec0c932ce01';
    const LEVEL2_ID = '000096cf-32a3-43bd-9034-4df343e5fd94';
    const LEVEL3_ID = '000096cf-32a3-43bd-9034-4df343e5fd93';
    const LEVEL4_ID = '000096cf-32a3-43ba-9034-4df343e5fd93';

    public function load(ObjectManager $manager)
    {
        $level0 = [
            'name' => 'Bronze',
            'description' => 'Starting level',
            'conditionValue' => 0,
            'minOrder' => 100,
            'reward' => [
                'name' => '5% off for every purchase',
                'value' => 0.05,
                'code' => '12345',
            ],
            'specialRewards' => [
                0 => [
                    'name' => 'Woman\'s Day 2016',
                    'value' => 0.20,
                    'code' => '901112',
                    'startAt' => new \DateTime('2016-03-07'),
                    'endAt' => new \DateTime('2016-03-08'),
                    'active' => true,
                    'id' => 'e82c96cf-32a3-43bd-9034-4da343e5fd00',
                ],
            ],
        ];
        $level = [
            'name' => 'VIP',
            'description' => 'Customers who spend more than 5000 EUR',
            'conditionValue' => 5000,
            'reward' => [
                'name' => '20% off for every purchase and additional reward',
                'value' => 0.30,
                'code' => '45678',
            ],
        ];

        $level2 = [
            'name' => 'Gold',
            'description' => 'Customers who spend more than 1500 EUR',
            'conditionValue' => 1500,
            'reward' => [
                'name' => '15% off for every purchase',
                'value' => 0.15,
                'code' => '34567',
            ],
            'specialRewards' => [
                0 => [
                    'name' => 'Father\'s Day 2016',
                    'value' => 0.20,
                    'code' => '78901',
                    'startAt' => new \DateTime('2016-03-18'),
                    'endAt' => new \DateTime('2016-03-19'),
                    'active' => true,
                    'id' => 'e82c96cf-32a3-43bd-9034-4da343e5ff00',
                ],
                1 => [
                    'name' => 'Mother\'s Day 2016',
                    'value' => 0.20,
                    'code' => '89011',
                    'startAt' => new \DateTime('2016-05-25'),
                    'endAt' => new \DateTime('2016-05-26'),
                    'active' => true,
                    'id' => 'e82c96cf-32a3-43bd-9034-4da343e5ff10',
                ],
            ],
        ];

        $level3 = [
            'name' => 'Silver',
            'description' => 'Customers who spend more than 400 EUR',
            'conditionValue' => 400,
            'reward' => [
                'name' => '10% off for every purchase',
                'value' => 0.10,
                'code' => '23456',
            ],
        ];

        $commandBud = $this->container->get('broadway.command_handling.command_bus');
        $commandBud->dispatch(
            new CreateLevel(new LevelId(self::LEVEL_ID), $level)
        );
        $commandBud->dispatch(
            new ActivateLevel(new LevelId(self::LEVEL_ID))
        );
        $commandBud->dispatch(
            new CreateLevel(new LevelId(self::LEVEL2_ID), $level2)
        );
        $commandBud->dispatch(
            new ActivateLevel(new LevelId(self::LEVEL2_ID))
        );
        $commandBud->dispatch(
            new CreateLevel(new LevelId(self::LEVEL3_ID), $level0)
        );
        $commandBud->dispatch(
            new ActivateLevel(new LevelId(self::LEVEL3_ID))
        );
        $commandBud->dispatch(
            new CreateLevel(new LevelId(self::LEVEL4_ID), $level3)
        );
        $commandBud->dispatch(
            new ActivateLevel(new LevelId(self::LEVEL4_ID))
        );
    }

    /**
     * Get the order of this fixture.
     *
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }
}
