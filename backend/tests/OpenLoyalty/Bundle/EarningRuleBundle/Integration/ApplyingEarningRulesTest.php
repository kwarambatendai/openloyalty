<?php

namespace OpenLoyalty\Bundle\EarningRuleBundle\Integration;

use OpenLoyalty\Bundle\BaseApiTest;
use OpenLoyalty\Domain\Account\ReadModel\PointsTransferDetails;
use OpenLoyalty\Domain\Account\ReadModel\PointsTransferDetailsRepository;

/**
 * Class ApplyingEarningRulesTest.
 */
class ApplyingEarningRulesTest extends BaseApiTest
{
    /**
     * @test
     */
    public function it_adds_points_after_transaction()
    {
        $formData = [
            'transactionData' => [
                'documentNumber' => '123',
                'documentType' => 'sell',
                'purchaseDate' => (new \DateTime('+1 day'))->format('Y-m-d'),
                'purchasePlace' => 'wroclaw',
            ],
            'items' => [
                0 => [
                    'sku' => ['code' => '12113'],
                    'name' => 'sku',
                    'quantity' => 1,
                    'grossValue' => 3,
                    'category' => 'test',
                    'maker' => 'company',
                ],
                1 => [
                    'sku' => ['code' => '11223233'],
                    'name' => 'sku',
                    'quantity' => 1,
                    'grossValue' => 20,
                    'category' => 'test',
                    'maker' => 'company',
                ],
                2 => [
                    'sku' => ['code' => 'SKU123'],
                    'name' => 'sku',
                    'quantity' => 1,
                    'grossValue' => 20,
                    'category' => 'test',
                    'maker' => 'company',
                ],
            ],
            'customerData' => [
                'name' => 'Jan Nowak',
                'email' => 'user-temp@oloy.com',
                'nip' => 'aaa',
                'phone' => '123',
                'loyaltyCardNumber' => 'not-present-in-system',
                'address' => [
                    'street' => 'Bagno',
                    'address1' => '12',
                    'city' => 'Warszawa',
                    'country' => 'PL',
                    'province' => 'Mazowieckie',
                    'postal' => '00-800',
                ],
            ],
        ];

        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/transaction',
            [
                'transaction' => $formData,
            ]
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        static::$kernel->boot();
        /** @var PointsTransferDetailsRepository $repo */
        $repo = static::$kernel->getContainer()->get('oloy.points.account.repository.points_transfer_details');
        /** @var PointsTransferDetails $points */
        $points = $repo->findBy(['transactionId' => $data['transactionId']]);

        $this->assertTrue(count($points) > 0);
        $points = reset($points);
        $this->assertEquals(144.9, $points->getValue(), 'There should be 144.9 points for this transaction, but there are '.$points->getValue());
    }
}
