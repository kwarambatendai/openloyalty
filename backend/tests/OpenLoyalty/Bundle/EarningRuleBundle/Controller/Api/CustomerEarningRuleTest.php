<?php

namespace OpenLoyalty\Bundle\EarningRuleBundle\Controller\Api;

use OpenLoyalty\Bundle\BaseApiTest;
use OpenLoyalty\Bundle\UserBundle\DataFixtures\ORM\LoadUserData;

/**
 * Class CustomerEarningRuleTest.
 */
class CustomerEarningRuleTest extends BaseApiTest
{
    /**
     * @test
     */
    public function it_returns_earning_rules()
    {
        $client = $this->createAuthenticatedClient(LoadUserData::USER_USERNAME, LoadUserData::USER_PASSWORD, 'customer');
        $client->request(
            'GET',
            '/api/customer/earningRule'
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $this->assertArrayHasKey('earningRules', $data);
        $this->assertTrue(count($data['earningRules']) > 0, 'There should be at least one earning rule');
    }
}
