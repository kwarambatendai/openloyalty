<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\PosBundle\DataFixtures\ORM;

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
    const POS2_ID = '00000000-0000-474c-1111-b0dd880c07e3';

    public function load(ObjectManager $manager)
    {
        $commandBus = $this->container->get('broadway.command_handling.command_bus');
        $commandBus->dispatch(
            new CreatePos(new PosId(static::POS_ID), $this->getPosData())
        );

        $posData = $this->getPosData();
        $posData['name'] = 'test1';
        $posData['location']['city'] = 'Warszawa';
        $posData['identifier'] = 'pos2';

        $commandBus->dispatch(
            new CreatePos(new PosId(static::POS2_ID), $posData)
        );
    }

    protected function getPosData()
    {
        return [
            'name' => 'test2',
            'identifier' => 'pos1',
            'description' => 'test',
            'location' => $this->getLocationData(),
        ];
    }

    protected function getLocationData()
    {
        return [
            'street' => 'Dmowskiego',
            'address1' => '21',
            'city' => 'Wrocław',
            'country' => 'PL',
            'postal' => '50-300',
            'province' => 'Dolnośląskie',
            'lat' => '51.1170364',
            'long' => '17.0203959',
        ];
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
