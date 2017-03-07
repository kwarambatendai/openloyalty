<?php

namespace OpenLoyalty\Bundle\LevelBundle\Controller\Api;

use OpenLoyalty\Bundle\BaseApiTest;
use OpenLoyalty\Bundle\LevelBundle\DataFixtures\ORM\LoadLevelData;
use OpenLoyalty\Domain\Level\Level;
use OpenLoyalty\Domain\Level\LevelId;
use OpenLoyalty\Domain\Level\LevelRepository;

/**
 * Class LevelControllerTest.
 */
class LevelControllerTest extends BaseApiTest
{
    /**
     * @test
     */
    public function it_creates_new_level()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/level/create',
            [
                'level' => [
                    'name' => 'test level',
                    'description' => 'test level',
                    'conditionValue' => 15,
                    'reward' => [
                        'name' => 'new reward',
                        'value' => 15,
                        'code' => 'xyz'
                    ],
                ],
            ]
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $this->assertArrayHasKey('id', $data);
    }


    /**
     * @test
     */
    public function it_return_level_data()
    {
        static::$kernel->boot();
        /** @var LevelRepository $repo */
        $repo = static::$kernel->getContainer()->get('oloy.level.repository');
        /** @var Level $level */
        $level = $repo->findAll()[0];
        $client = $this->createAuthenticatedClient();
        $client->request(
            'GET',
            '/api/level/'.$level->getLevelId()->__toString()
        );
        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200'.$response->getContent());
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals($level->getLevelId()->__toString(), $data['id']);
    }

    /**
     * @test
     */
    public function it_creates_new_level_and_set_reward_data()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/level/create',
            [
                'level' => [
                    'name' => 'test level',
                    'description' => 'test level',
                    'conditionValue' => 15,
                    'reward' => [
                        'name' => 'new reward',
                        'value' => 15,
                        'code' => 'xyz'
                    ],
                ],
            ]
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $this->assertArrayHasKey('id', $data);
        $client->request(
            'GET',
            '/api/level/'.$data['id']
        );
        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals('new reward', $data['reward']['name']);
        $this->assertEquals(0.15, $data['reward']['value']);
        $this->assertEquals('xyz', $data['reward']['code']);
    }

    /**
     * @test
     */
    public function it_creates_new_level_and_add_new_special_rewards()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/level/create',
            [
                'level' => [
                    'name' => 'test level',
                    'description' => 'test level',
                    'conditionValue' => 15,
                    'reward' => [
                        'name' => 'new reward',
                        'value' => 15,
                        'code' => 'xyz'
                    ],
                    'specialRewards' => [
                        0 => [
                            'name' => 'special reward - added',
                            'value' => 20,
                            'code' => 'spec',
                            'startAt' => '2016-10-10',
                            'endAt' => '2016-11-10',
                            'active' => true
                        ],
                        1 => [
                            'name' => 'special reward - added 2',
                            'value' => 10,
                            'code' => 'spec2',
                            'startAt' => '2016-09-10',
                            'endAt' => '2016-11-10',
                            'active' => false
                        ],
                    ]
                ],
            ]
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $this->assertArrayHasKey('id', $data);
        $level = $this->getLevel($data['id']);

        $this->assertTrue(count($level->getSpecialRewards()) == 2, 'There should be 2 special rewards');
        $specialRewards = $level->getSpecialRewards();
        $this->assertInstanceOf(\DateTime::class, $specialRewards[0]->getStartAt());
        $this->assertInstanceOf(\DateTime::class, $specialRewards[0]->getEndAt());
        $this->assertInstanceOf(\DateTime::class, $specialRewards[1]->getStartAt());
        $this->assertInstanceOf(\DateTime::class, $specialRewards[1]->getEndAt());
        $this->assertEquals(true, $specialRewards[0]->isActive());
        $this->assertEquals('special reward - added 2', $specialRewards[1]->getName());
    }

    /**
     * @test
     */
    public function it_returns_levels_list()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'GET',
            '/api/level'
        );
        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $this->assertArrayHasKey('levels', $data);
        $this->assertTrue(count($data['levels']) > 0, 'Contains at least one element');
    }

    /**
     * @test
     */
    public function it_updates_level_name()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'PUT',
            '/api/level/'.LoadLevelData::LEVEL_ID,
            [
                'level' => [
                    'name' => 'updated level name',
                    'description' => 'test level',
                    'conditionValue' => 0.15,
                    'reward' => [
                        'name' => 'new reward',
                        'value' => 15,
                        'code' => 'xyz'
                    ],
                ]
            ]
        );
        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals(LoadLevelData::LEVEL_ID, $data['id']);
        $level = $this->getLevel(LoadLevelData::LEVEL_ID);
        $this->assertEquals('updated level name', $level->getName(), 'Name should be now "updated level name"');
    }

    /**
     * @test
     */
    public function it_updates_level_special_rewards()
    {
        static::$kernel->boot();
        $client = $this->createAuthenticatedClient();
        $client->request(
            'PUT',
            '/api/level/'.LoadLevelData::LEVEL2_ID,
            [
                'level' => [
                    'name' => 'updated level name',
                    'description' => 'test level',
                    'conditionValue' => 0.15,
                    'reward' => [
                        'name' => 'new reward - edited',
                        'value' => 2,
                        'code' => 'xyz'
                    ],
                    'specialRewards' => [
                        0 => [
                            'name' => 'special reward - updated',
                            'value' => 90,
                            'code' => 'spec',
                            'startAt' => '2016-10-10',
                            'endAt' => '2016-11-10',
                            'active' => true
                        ],
                    ]
                ]
            ]
        );
        $response = $client->getResponse();

        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals(LoadLevelData::LEVEL2_ID, $data['id']);
        $level = $this->getLevel(LoadLevelData::LEVEL2_ID);
        $this->assertTrue(count($level->getSpecialRewards()) == 1, 'There should be 1 special rewards');
        $specialRewards = $level->getSpecialRewards();
        $this->assertInstanceOf(\DateTime::class, $specialRewards[0]->getStartAt());
        $this->assertInstanceOf(\DateTime::class, $specialRewards[0]->getEndAt());
        $this->assertEquals(true, $specialRewards[0]->isActive());
        $this->assertEquals('special reward - updated', $specialRewards[0]->getName());
    }

    /**
     * @test
     */
    public function it_updates_reward()
    {
        static::$kernel->boot();
        $client = $this->createAuthenticatedClient();
        $client->request(
            'PUT',
            '/api/level/'.LoadLevelData::LEVEL_ID,
            [
                'level' => [
                    'name' => 'updated level name',
                    'description' => 'test level',
                    'conditionValue' => 0.15,
                    'reward' => [
                        'name' => 'new reward - edited',
                        'value' => 2,
                        'code' => 'xyz'
                    ],
                ]
            ]
        );
        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals(LoadLevelData::LEVEL_ID, $data['id']);
        $level = $this->getLevel(LoadLevelData::LEVEL_ID);

        $this->assertEquals('new reward - edited', $level->getReward()->getName(), 'Name should be now "new reward - edited"');
        $this->assertEquals(0.02, $level->getReward()->getValue(), 'Value should be now "0.02"');
    }

    /**
     * @test
     */
    public function it_returns_bad_request_on_empty_name()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/level/create',
            [
                'level' => [
                    'description' => 'test level',
                ],
            ]
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(400, $response->getStatusCode(), 'Response should have status 400');
        $this->assertTrue(count($data['form']['children']['name']['errors']) > 0, 'There should be an error on name field');
    }

    /**
     * @param $id
     *
     * @return Level
     */
    protected function getLevel($id)
    {
        static::$kernel->boot();
        /** @var LevelRepository $repo */
        $repo = static::$kernel->getContainer()->get('oloy.level.repository');
        /** @var Level $level */
        $level = $repo->byId(new LevelId($id));
        return $level;
    }
}
