<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\DemoBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenLoyalty\Domain\Pos\Command\CreatePos;
use OpenLoyalty\Domain\Pos\PosId;
use Symfony\Bridge\Doctrine\Tests\Fixtures\ContainerAwareFixture;

/**
 * Class LoadPosData.
 */
class LoadPosData extends ContainerAwareFixture implements OrderedFixtureInterface
{
    const POS_ID = '00000000-0000-474c-1111-b0dd880c07e2';
    const POS_ID1 = '00000000-0000-474c-1111-b0dd880c07a2';
    const POS2_ID = '00000000-0000-474c-1111-b0dd880c87b2';
    const POS2_ID3 = '00000000-0000-474c-1111-b0dd880c87c2';

    public function load(ObjectManager $manager)
    {
        $commandBus = $this->container->get('broadway.command_handling.command_bus');
        $commandBus->dispatch(
            new CreatePos(new PosId(static::POS_ID), $this->getPosData())
        );

        $commandBus = $this->container->get('broadway.command_handling.command_bus');
        $commandBus->dispatch(
            new CreatePos(new PosId(static::POS2_ID), $this->getPos2Data())
        );
        $commandBus = $this->container->get('broadway.command_handling.command_bus');
        $commandBus->dispatch(
            new CreatePos(new PosId(static::POS2_ID3), $this->getPos3Data())
        );
        $commandBus = $this->container->get('broadway.command_handling.command_bus');
        $commandBus->dispatch(
            new CreatePos(new PosId(static::POS_ID1), $this->getPos4Data())
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
    private function getPosData()
    {
        return [
            'name' => 'Off-line store 1',
            'identifier' => 'pos1',
            'description' => 'Sample POS',
            'location' => [
                'street' => 'Street',
                'address1' => '21',
                'city' => 'City',
                'country' => 'US',
                'postal' => '00015',
                'province' => 'NY',
                'lat' => '51.1170364',
                'long' => '17.0203959',
            ],
        ];
    }
    private function getPos2Data()
    {
        return [
            'name' => 'eCommerce 2',
            'identifier' => 'ecommerce2',
            'description' => 'Sample on-line POS',
            'location' => [
                'street' => 'Street',
                'address1' => '3',
                'city' => 'City',
                'country' => 'UK',
                'postal' => '00002',
                'province' => 'London - City',
                'lat' => '51.1170364',
                'long' => '17.0203959',
            ],
        ];
    }

    private function getPos3Data()
    {
        return [
            'name' => 'Off-line store 2',
            'identifier' => 'france_1',
            'description' => 'Sample POS',
            'location' => [
                'street' => 'Street',
                'address1' => '21',
                'city' => 'City',
                'country' => 'FR',
                'postal' => '12345',
                'province' => 'Paris',
                'lat' => '51.1170364',
                'long' => '17.0203959',
            ],
        ];
    }

    private function getPos4Data()
    {
        return [
            'name' => 'eCommerce 1',
            'identifier' => 'us_online_1',
            'description' => 'Sample POS',
            'location' => [
                'street' => 'Street',
                'address1' => '21',
                'city' => 'City',
                'country' => 'US',
                'postal' => '12345',
                'province' => 'Washington',
                'lat' => '51.1170364',
                'long' => '17.0203959',
            ],
        ];
    }
}
