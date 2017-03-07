<?php

namespace OpenLoyalty\Domain\Pos\Command;

use OpenLoyalty\Domain\Pos\Model\Location;
use OpenLoyalty\Domain\Pos\Pos;
use OpenLoyalty\Domain\Pos\PosId;

/**
 * Class CreatePosTest.
 */
class CreatePosTest extends PosCommandHandlerTest
{
    /**
     * @test
     */
    public function it_creates_pos()
    {
        $handler = $this->createCommandHandler();
        $posId = new PosId('00000000-0000-0000-0000-000000000000');

        $command = new CreatePos($posId, $this->getPosData());
        $handler->handle($command);
        $pos = $this->inMemoryRepository->byId($posId);
        $this->assertNotNull($pos);
        $this->assertInstanceOf(Pos::class, $pos);
        $this->assertInstanceOf(Location::class, $pos->getLocation());
    }

    /**
     * @test
     * @expectedException \Assert\InvalidArgumentException
     */
    public function it_throws_exception_when_location_is_not_provided()
    {
        $handler = $this->createCommandHandler();
        $posId = new PosId('00000000-0000-0000-0000-000000000000');

        $command = new CreatePos($posId, $this->getPosDataWithoutLocation());
        $handler->handle($command);
    }

    protected function getPosData()
    {
        return [
            'name' => 'test',
            'identifier' => 'pos1',
            'description' => 'test',
            'location' => $this->getLocationData(),
        ];
    }

    protected function getPosDataWithoutLocation()
    {
        return [
            'name' => 'test',
            'identifier' => 'pos1',
            'description' => 'test',
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
            'lat' => '11.11',
            'long' => '12.11',
        ];
    }
}
