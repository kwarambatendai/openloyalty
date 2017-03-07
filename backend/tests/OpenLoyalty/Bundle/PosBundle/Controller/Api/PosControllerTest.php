<?php

namespace OpenLoyalty\Bundle\PosBundle\Controller\Api;

use OpenLoyalty\Bundle\BaseApiTest;
use OpenLoyalty\Bundle\PosBundle\DataFixtures\ORM\LoadPosData;
use OpenLoyalty\Domain\Pos\Pos;
use OpenLoyalty\Domain\Pos\PosId;
use OpenLoyalty\Domain\Pos\PosRepository;

/**
 * Class PosControllerTest.
 */
class PosControllerTest extends BaseApiTest
{
    /**
     * @var PosRepository
     */
    protected $repository;

    protected function setUp()
    {
        parent::setUp();

        static::bootKernel();
        $this->repository = static::$kernel->getContainer()->get('oloy.pos.repository');
    }

    /**
     * @test
     */
    public function it_creates_pos()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/pos',
            [
                'pos' =>  [
                    'name' => 'new pos in wroclaw',
                    'identifier' => 'pos2',
                    'location' => $this->getLocationData(),
                ],
            ]
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $this->assertArrayHasKey('posId', $data);
        $pos = $this->repository->byId(new PosId($data['posId']));
        $this->assertInstanceOf(Pos::class, $pos);
        $this->assertEquals('new pos in wroclaw', $pos->getName());
    }

    /**
     * @test
     */
    public function it_updates_pos()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'PUT',
            '/api/pos/'.LoadPosData::POS_ID,
            [
                'pos' =>  [
                    'name' => 'updated name',
                    'identifier' => 'updated identifier',
                    'location' => $this->getLocationData(),
                ],
            ]
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $this->assertArrayHasKey('posId', $data);
        /** @var Pos $pos */
        $pos = $this->repository->byId(new PosId(LoadPosData::POS_ID));
        $this->assertInstanceOf(Pos::class, $pos);
        $this->assertEquals('updated name', $pos->getName());
        $this->assertEquals('updated identifier', $pos->getIdentifier());
    }

    /**
     * @test
     */
    public function it_returns_pos()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'GET',
            '/api/pos/'.LoadPosData::POS_ID
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $this->assertArrayHasKey('name', $data);
    }

    /**
     * @test
     */
    public function it_returns_pos_list()
    {
        $client = $this->createAuthenticatedClient();
        $client->insulate();
        $client->request(
            'GET',
            '/api/pos'
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $this->assertArrayHasKey('pos', $data);
        $this->assertTrue(count($data['pos']) > 0, 'There should be at least one pos');
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
}
