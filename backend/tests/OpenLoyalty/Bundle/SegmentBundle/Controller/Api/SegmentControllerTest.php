<?php

namespace OpenLoyalty\Bundle\SegmentBundle\Controller\Api;

use Doctrine\Common\Collections\Collection;
use OpenLoyalty\Bundle\BaseApiTest;
use OpenLoyalty\Bundle\PosBundle\DataFixtures\ORM\LoadPosData;
use OpenLoyalty\Bundle\SegmentBundle\DataFixtures\ORM\LoadSegmentData;
use OpenLoyalty\Domain\Segment\Model\Criteria\Anniversary;
use OpenLoyalty\Domain\Segment\Model\Criteria\TransactionCount;
use OpenLoyalty\Domain\Segment\Model\Criterion;
use OpenLoyalty\Domain\Segment\Segment;
use OpenLoyalty\Domain\Segment\SegmentId;
use OpenLoyalty\Domain\Segment\SegmentRepository;

/**
 * Class SegmentControllerTest.
 */
class SegmentControllerTest extends BaseApiTest
{
    /**
     * @var SegmentRepository
     */
    protected $repository;

    protected function setUp()
    {
        parent::setUp();

        static::bootKernel();
        $this->repository = static::$kernel->getContainer()->get('oloy.segment.repository');
    }

    /**
     * @test
     */
    public function it_creates_segment()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/segment',
            [
                'segment' => [
                    'name' => 'test1234',
                    'description' => 'desc',
                    'parts' => [
                        [
                            'criteria' => [
                                [
                                    'type' => Criterion::TYPE_BOUGHT_IN_POS,
                                    'posIds' => [LoadPosData::POS_ID],
                                ],
                                [
                                    'type' => Criterion::TYPE_AVERAGE_TRANSACTION_AMOUNT,
                                    'fromAmount' => 1,
                                    'toAmount' => 10000,
                                ],
                                [
                                    'type' => Criterion::TYPE_TRANSACTION_COUNT,
                                    'min' => 10,
                                    'max' => 20,
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $this->assertArrayHasKey('segmentId', $data);
        /** @var Segment $segment */
        $segment = $this->repository->byId(new SegmentId($data['segmentId']));
        $this->assertInstanceOf(Segment::class, $segment);
        $this->assertEquals('test1234', $segment->getName());
        $this->assertEquals(1, count($segment->getParts()));
        $segmentParts = $segment->getParts();

        if ($segmentParts instanceof Collection) {
            $part = $segmentParts->first();
        } else {
            $part = reset($segmentParts);
        }

        $this->assertEquals(count($part->getCriteria()), 3);
    }

    /**
     * @test
     */
    public function it_creates_segment_with_all_criteria()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/segment',
            [
                'segment' => [
                    'name' => 'test2345',
                    'description' => 'desc',
                    'parts' => [
                        [
                            'criteria' => [
                                [
                                    'type' => Criterion::TYPE_BOUGHT_IN_POS,
                                    'posIds' => [LoadPosData::POS_ID],
                                ],
                                [
                                    'type' => Criterion::TYPE_AVERAGE_TRANSACTION_AMOUNT,
                                    'fromAmount' => 1,
                                    'toAmount' => 10000,
                                ],
                                [
                                    'type' => Criterion::TYPE_TRANSACTION_COUNT,
                                    'min' => 10,
                                    'max' => 20,
                                ],
                                [
                                    'type' => Criterion::TYPE_PURCHASE_PERIOD,
                                    'fromDate' => '2016-01-01 10:00',
                                    'toDate' => '2016-02-01 10:00',
                                ],
                                [
                                    'type' => Criterion::TYPE_LAST_PURCHASE_N_DAYS_BEFORE,
                                    'days' => 10,
                                ],
                                [
                                    'type' => Criterion::TYPE_TRANSACTION_AMOUNT,
                                    'fromAmount' => 1,
                                    'toAmount' => 1000,
                                ],
                                [
                                    'type' => Criterion::TYPE_ANNIVERSARY,
                                    'anniversaryType' => Anniversary::TYPE_BIRTHDAY,
                                    'days' => 20,
                                ],
                                [
                                    'type' => Criterion::TYPE_TRANSACTION_PERCENT_IN_POS,
                                    'posId' => LoadPosData::POS_ID,
                                    'percent' => 10,
                                ],
                                [
                                    'type' => Criterion::TYPE_BOUGHT_SKUS,
                                    'skuIds' => ['123'],
                                ],
                                [
                                    'type' => Criterion::TYPE_BOUGHT_MAKERS,
                                    'makers' => ['company'],
                                ],
                                [
                                    'type' => Criterion::TYPE_BOUGHT_LABELS,
                                    'labels' => [[
                                        'key' => 'test',
                                        'value' => 'label',
                                    ]],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $this->assertArrayHasKey('segmentId', $data);
        /** @var Segment $segment */
        $segment = $this->repository->byId(new SegmentId($data['segmentId']));
        $this->assertInstanceOf(Segment::class, $segment);
        $this->assertEquals('test2345', $segment->getName());
        $this->assertEquals(1, count($segment->getParts()));
        $segmentParts = $segment->getParts();

        if ($segmentParts instanceof Collection) {
            $part = $segmentParts->first();
        } else {
            $part = reset($segmentParts);
        }

        $this->assertEquals(count($part->getCriteria()), 11);
    }

    /**
     * @test
     */
    public function it_updates_segment()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'PUT',
            '/api/segment/'.LoadSegmentData::SEGMENT_ID,
            [
                'segment' => [
                    'name' => 'test - updated',
                    'description' => 'desc',
                    'parts' => [
                        [
                            'criteria' => [
                                [
                                    'type' => Criterion::TYPE_BOUGHT_IN_POS,
                                    'posIds' => [LoadPosData::POS_ID],
                                ],
                                [
                                    'type' => Criterion::TYPE_AVERAGE_TRANSACTION_AMOUNT,
                                    'fromAmount' => 1,
                                    'toAmount' => 10000,
                                ],
                                [
                                    'type' => Criterion::TYPE_TRANSACTION_COUNT,
                                    'min' => 1,
                                    'max' => 100,
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $this->assertArrayHasKey('segmentId', $data);
        /** @var Segment $segment */
        $segment = $this->repository->byId(new SegmentId(LoadSegmentData::SEGMENT_ID));
        $this->assertInstanceOf(Segment::class, $segment);
        $this->assertEquals('test - updated', $segment->getName());
        $this->assertEquals(1, count($segment->getParts()));
        $segmentParts = $segment->getParts();

        if ($segmentParts instanceof Collection) {
            $part = $segmentParts->first();
        } else {
            $part = reset($segmentParts);
        }

        $this->assertEquals(count($part->getCriteria()), 3);
        foreach ($part->getCriteria() as $criterion) {
            if ($criterion instanceof TransactionCount) {
                $this->assertEquals(1, $criterion->getMin());
            }
        }
    }

    /**
     * @test
     */
    public function it_deactivates_segment()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/segment/'.LoadSegmentData::SEGMENT_ID.'/deactivate'
        );

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        /** @var Segment $segment */
        $segment = $this->repository->byId(new SegmentId(LoadSegmentData::SEGMENT_ID));
        $this->assertInstanceOf(Segment::class, $segment);
        $this->assertEquals(false, $segment->isActive());
    }

    /**
     * @test
     */
    public function it_returns_segment()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'GET',
            '/api/segment/'.LoadSegmentData::SEGMENT_ID
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $this->assertArrayHasKey('segmentId', $data);
        $this->assertArrayHasKey('parts', $data);
    }

    /**
     * @test
     */
    public function it_returns_list_of_segments()
    {
        $client = $this->createAuthenticatedClient();
        $client->insulate();
        $client->request(
            'GET',
            '/api/segment'
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $this->assertArrayHasKey('segments', $data);
        $this->assertTrue(count($data['segments']) > 0);
        $this->assertArrayHasKey('segmentId', $data['segments'][0]);
    }

    /**
     * @test
     */
    public function it_activates_segment()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/segment/'.LoadSegmentData::SEGMENT_ID.'/activate'
        );

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        /** @var Segment $segment */
        $segment = $this->repository->byId(new SegmentId(LoadSegmentData::SEGMENT_ID));
        $this->assertInstanceOf(Segment::class, $segment);
        $this->assertEquals(true, $segment->isActive());
    }
}
