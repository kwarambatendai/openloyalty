<?php

namespace OpenLoyalty\Bundle;

use OpenLoyalty\Bundle\UserBundle\DataFixtures\ORM\LoadUserData;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

/**
 * Class BaseApiTest.
 */
abstract class BaseApiTest extends WebTestCase
{
    protected function createAuthenticatedClient($username = LoadUserData::ADMIN_USERNAME, $password = LoadUserData::ADMIN_PASSWORD, $type = 'admin')
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/'.$type.'/login_check',
            array(
                '_username' => $username,
                '_password' => $password,
            )
        );

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue(isset($data['token']), 'Response should have field "token". '.$client->getResponse()->getContent().json_encode(['/api/'.$type.'/login_check',
                array(
                    '_username' => $username,
                    '_password' => $password,
                ), ]));
        $this->assertTrue(isset($data['refresh_token']), 'Response should have field "refresh_token". '.$client->getResponse()->getContent());

        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }

    /**
     * @param Client $client
     * @param string $customerId
     *
     * @return string
     */
    protected function getCustomerPoints(Client $client, $customerId)
    {
        $client->request(
            'GET',
            '/api/customer/'.$customerId.'/status'
        );
        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $this->assertArrayHasKey('points', $data);

        return $data['points'];
    }

    /**
     * @param $customerEmail
     *
     * @return string
     */
    protected function getActivateTokenForCustomer($customerEmail)
    {
        $em = static::$kernel->getContainer()->get('doctrine.orm.default_entity_manager');
        $activateToken = $em
            ->getRepository('OpenLoyaltyUserBundle:Customer')
            ->findOneBy(['email' => $customerEmail])
            ->getActionToken();

        return $activateToken;
    }

    /**
     * @param string $customerId
     *
     * @return null|object
     */
    protected function getCustomerEntity($customerId)
    {
        $customer = static::$kernel->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository('OpenLoyaltyUserBundle:Customer')->findOneBy(['id' => $customerId]);

        return $customer;
    }
}
