<?php

namespace OpenLoyalty\Bundle;

use Symfony\Component\HttpKernel\Client;

/**
 * Class BaseAccessControlTest.
 */
abstract class BaseAccessControlTest extends BaseApiTest
{
    public function checkClients(array $clients, $route, $params = [], $method = 'GET')
    {
        foreach ($clients as $clientData) {
             /** @var Client $client */
            $client = $clientData['client'];
            $client->insulate(true);
            $client->request(
                $method,
                $route,
                $params
            );
            $statusCode = $client->getResponse()->getStatusCode();

            if (isset($clientData['status'])) {
                $this->assertTrue(
                    $clientData['status'] == $statusCode,
                    $clientData['status'].' should be returned instead '.$statusCode.', client:'.(isset($clientData['name']) ? $clientData['name'] : null).$client->getResponse()->getStatusCode()
                );
            } elseif (isset($clientData['not_status'])) {
                $this->assertTrue(
                    $clientData['not_status'] != $statusCode,
                    $clientData['not_status'].' should not be returned, instead '.$statusCode.' returned, client:'.(isset($clientData['name']) ? $clientData['name'] : null)
                );
            }
        }
    }

    public function getCustomerClient()
    {
        return $this->createAuthenticatedClient('user@oloy.com', 'loyalty', 'customer');
    }

    public function getAdminClient()
    {
        return $this->createAuthenticatedClient();
    }

    public function getSellerClient()
    {
        return $this->createAuthenticatedClient('john@doe.com', 'open', 'seller');
    }
}
