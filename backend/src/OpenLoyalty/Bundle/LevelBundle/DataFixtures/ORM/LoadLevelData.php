<?php

namespace OpenLoyalty\Bundle\LevelBundle\DataFixtures\ORM;

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
    const LEVEL_ID = 'e82c96cf-32a3-43bd-9034-4df343e5fd94';
    const LEVEL2_ID = '000096cf-32a3-43bd-9034-4df343e5fd94';
    const LEVEL3_ID = '000096cf-32a3-43bd-9034-4df343e5fd93';

    public function load(ObjectManager $manager)
    {
        $level0 = [
            'name' => 'level0',
            'description' => 'example level',
            'conditionValue' => 0,
            'reward' => [
                'name' => 'test reward',
                'value' => 0.14,
                'code' => 'abc',
            ],
        ];

        $level = [
            'name' => 'level1',
            'description' => 'example level',
            'conditionValue' => 20,
            'reward' => [
                'name' => 'test reward',
                'value' => 0.15,
                'code' => 'abc',
            ],
        ];
        $level2 = [
            'name' => 'level2',
            'description' => 'example level',
            'conditionValue' => 200,
            'reward' => [
                'name' => 'test reward',
                'value' => 0.20,
                'code' => 'abc',
            ],
            'specialRewards' => [
                0 => [
                    'name' => 'special reward',
                    'value' => 0.22,
                    'code' => 'spec',
                    'startAt' => new \DateTime('2016-10-10'),
                    'endAt' => new \DateTime('2016-11-10'),
                    'active' => true,
                    'id' => 'e82c96cf-32a3-43bd-9034-4df343e5fd00',
                ],
                1 => [
                    'name' => 'special reward 2',
                    'value' => 0.11,
                    'code' => 'spec2',
                    'startAt' => new \DateTime('2016-09-10'),
                    'endAt' => new \DateTime('2016-11-10'),
                    'active' => false,
                    'id' => 'e82c96cf-32a3-43bd-9034-4df343e50094',
                ],
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
    }

    /**
     * Get the order of this fixture.
     *
     * @return int
     */
    public function getOrder()
    {
        return 0;
    }
}
