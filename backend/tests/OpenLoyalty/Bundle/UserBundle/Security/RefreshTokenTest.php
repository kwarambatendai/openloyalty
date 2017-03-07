<?php

namespace OpenLoyalty\Bundle\UserBundle\Security;

use OpenLoyalty\Bundle\BaseApiTest;
use OpenLoyalty\Bundle\UserBundle\DataFixtures\ORM\LoadUserData;

/**
 * Class RefreshTokenTest
 * @package OpenLoyalty\Bundle\UserBundle\Security
 */
class RefreshTokenTest extends BaseApiTest
{
    /**
     * @test
     */
    public function it_allows_to_obtain_new_token_based_on_refresh_token()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/admin/login_check',
            array(
                '_username' => LoadUserData::ADMIN_USERNAME,
                '_password' => LoadUserData::ADMIN_PASSWORD,
            )
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        $token = $data['token'];
        $refreshToken = $data['refresh_token'];

        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        $client->request(
            'POST',
            '/api/token/refresh',
            [
                'refresh_token' => $refreshToken
            ]
        );

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue(isset($data['refresh_token']), 'Response should have field "refresh_token". '.$client->getResponse()->getContent());
    }
}
