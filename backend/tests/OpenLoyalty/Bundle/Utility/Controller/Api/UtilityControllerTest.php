<?php

namespace OpenLoyalty\Bundle\Utility\Controller\Api;

use OpenLoyalty\Bundle\BaseApiTest;
use OpenLoyalty\Bundle\LevelBundle\DataFixtures\ORM\LoadLevelData;
use OpenLoyalty\Bundle\SegmentBundle\DataFixtures\ORM\LoadSegmentData;

/**
 * Class TransactionControllerTest.
 */
class UtilityontrollerTest extends BaseApiTest
{
    /**
     * @test
     */
    public function it_returns_segments_csv()
    {
        ob_start();
        $client = $this->createAuthenticatedClient();
        $client->request(
            'GET',
            '/api/csv/segment/'.LoadSegmentData::SEGMENT_ID
        );
        $response = $client->getResponse();
        $statusCode = $response->getStatusCode();
        $contentType = $response->headers->get('Content-Type');
        ob_get_clean();
        $this->assertEquals(200, $statusCode);
        $this->assertEquals('text/csv; charset=utf-8', $contentType);
    }
    /**
     * @test
     */
    public function it_returns_levels_csv()
    {
        ob_start();
        $client = $this->createAuthenticatedClient();
        $client->request(
            'GET',
            '/api/csv/level/'.LoadLevelData::LEVEL_ID
        );
        $response = $client->getResponse();
        $statusCode = $response->getStatusCode();
        $contentType = $response->headers->get('Content-Type');
        ob_get_clean();
        $this->assertEquals(200, $statusCode);
        $this->assertEquals('text/csv; charset=utf-8', $contentType);
    }
}
