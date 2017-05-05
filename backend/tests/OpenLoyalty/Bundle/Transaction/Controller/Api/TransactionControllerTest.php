<?php

namespace OpenLoyalty\Bundle\Transaction\Controller\Api;

use OpenLoyalty\Bundle\BaseApiTest;
use OpenLoyalty\Bundle\PosBundle\DataFixtures\ORM\LoadPosData;
use OpenLoyalty\Bundle\SettingsBundle\Entity\JsonSettingEntry;
use OpenLoyalty\Bundle\UserBundle\DataFixtures\ORM\LoadUserData;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetails;
use OpenLoyalty\Bundle\SettingsBundle\Service\SettingsManager;

/**
 * Class TransactionControllerTest.
 */
class TransactionControllerTest extends BaseApiTest
{
    /**
     * @test
     */
    public function it_returns_transactions_list()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'GET',
            '/api/transaction'
        );
        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200'.$response->getContent());
        $this->assertArrayHasKey('transactions', $data);
        $this->assertTrue(count($data['transactions']) > 0, 'Contains at least one element');
        $this->assertTrue($data['total'] > 0, 'Contains at least one element');
    }

    /**
     * @test
     */
    public function it_register_new_transaction_without_setting_customer()
    {
        $formData = [
            'transactionData' => [
                'documentNumber' => '123',
                'documentType' => 'sell',
                'purchaseDate' => '2015-01-01',
                'purchasePlace' => 'wroclaw',
            ],
            'items' => [
                0 => [
                    'sku' => ['code' => '123'],
                    'name' => 'sku',
                    'quantity' => 1,
                    'grossValue' => 1,
                    'category' => 'test',
                    'maker' => 'company',
                    'labels' => [
                        [
                            'key' => 'test',
                            'value' => 'label',
                        ],
                    ],
                ],
                1 => [
                    'sku' => ['code' => '1123'],
                    'name' => 'sku',
                    'quantity' => 1,
                    'grossValue' => 11,
                    'category' => 'test',
                    'maker' => 'company',
                ],
            ],
            'customerData' => [
                'name' => 'Jan Nowak',
                'email' => 'user-temp2@oloy.com',
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
        $repo = static::$kernel->getContainer()->get('oloy.transaction.read_model.repository.transaction_details');
        /** @var TransactionDetails $transaction */
        $transaction = $repo->find($data['transactionId']);
        $this->assertInstanceOf(TransactionDetails::class, $transaction);
        $this->assertNull($transaction->getCustomerId());
    }

    /**
     * @test
     */
    public function it_register_new_transaction_with_only_required_data()
    {
        $formData = [
            'transactionData' => [
                'documentNumber' => '123',
                'purchaseDate' => '2015-01-01',
                'purchasePlace' => 'wroclaw',
            ],
            'items' => [
                0 => [
                    'sku' => ['code' => '123'],
                    'name' => 'sku',
                    'quantity' => 1,
                    'grossValue' => 1,
                    'category' => 'test',
                ],
                1 => [
                    'sku' => ['code' => '1123'],
                    'name' => 'sku',
                    'quantity' => 1,
                    'grossValue' => 11,
                    'category' => 'test',
                ],
            ],
            'customerData' => [
                'name' => 'Jan Nowak',
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
        $repo = static::$kernel->getContainer()->get('oloy.transaction.read_model.repository.transaction_details');
        /** @var TransactionDetails $transaction */
        $transaction = $repo->find($data['transactionId']);
        $this->assertInstanceOf(TransactionDetails::class, $transaction);
        $this->assertNull($transaction->getCustomerId());
    }

    /**
     * @test
     */
    public function it_register_new_return_transaction()
    {
        $formData = [
            'revisedDocument' => 'revised test',
            'transactionData' => [
                'documentNumber' => '123',
                'purchaseDate' => '2015-01-01',
                'purchasePlace' => 'wroclaw',
                'documentType' => 'return',
            ],
            'items' => [
                0 => [
                    'sku' => ['code' => '123'],
                    'name' => 'sku',
                    'quantity' => 1,
                    'grossValue' => -11,
                    'category' => 'test',
                ],
                1 => [
                    'sku' => ['code' => '1123'],
                    'name' => 'sku',
                    'quantity' => 1,
                    'grossValue' => -1,
                    'category' => 'test',
                ],
            ],
            'customerData' => [
                'name' => 'Jan Nowak',
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
        $repo = static::$kernel->getContainer()->get('oloy.transaction.read_model.repository.transaction_details');
        /** @var TransactionDetails $transaction */
        $transaction = $repo->find($data['transactionId']);
        $this->assertInstanceOf(TransactionDetails::class, $transaction);
        $this->assertNull($transaction->getCustomerId());
        $this->assertEquals('return', $transaction->getDocumentType());
        $this->assertEquals('revised test', $transaction->getRevisedDocument());
        $this->assertEquals(-12, $transaction->getGrossValue());
    }

    /**
     * @test
     */
    public function it_register_new_transaction_with_pos()
    {
        $formData = [
            'transactionData' => [
                'documentNumber' => '123',
                'documentType' => 'sell',
                'purchaseDate' => '2015-01-01',
                'purchasePlace' => 'wroclaw',
            ],
            'items' => [
                0 => [
                    'sku' => ['code' => '123'],
                    'name' => 'sku',
                    'quantity' => 1,
                    'grossValue' => 1,
                    'category' => 'test',
                    'maker' => 'company',
                ],
                1 => [
                    'sku' => ['code' => '1123'],
                    'name' => 'sku',
                    'quantity' => 1,
                    'grossValue' => 11,
                    'category' => 'test',
                    'maker' => 'company',
                ],
            ],
            'customerData' => [
                'name' => 'Jan Nowak',
                'email' => 'user-temp2@oloy.com',
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
            'pos' => LoadPosData::POS_ID,
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
        $repo = static::$kernel->getContainer()->get('oloy.transaction.read_model.repository.transaction_details');
        /** @var TransactionDetails $transaction */
        $transaction = $repo->find($data['transactionId']);
        $this->assertInstanceOf(TransactionDetails::class, $transaction);
        $this->assertNull($transaction->getCustomerId());
        $this->assertNotNull($transaction->getPosId());
    }

    /**
     * @test
     */
    public function it_register_new_transaction_and_assign_customer()
    {
        $formData = [
            'transactionData' => [
                'documentNumber' => '123',
                'documentType' => 'sell',
                'purchaseDate' => '2015-01-01',
                'purchasePlace' => 'wroclaw',
            ],
            'items' => [
                0 => [
                    'sku' => ['code' => '123'],
                    'name' => 'sku',
                    'quantity' => 1,
                    'grossValue' => 1,
                    'category' => 'test',
                    'maker' => 'company',
                ],
                1 => [
                    'sku' => ['code' => '1123'],
                    'name' => 'sku',
                    'quantity' => 1,
                    'grossValue' => 11,
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
        $repo = static::$kernel->getContainer()->get('oloy.transaction.read_model.repository.transaction_details');
        /** @var TransactionDetails $transaction */
        $transaction = $repo->find($data['transactionId']);
        $this->assertInstanceOf(TransactionDetails::class, $transaction);
        $this->assertNotNull($transaction->getCustomerId());
        $this->assertEquals(LoadUserData::TEST_USER_ID, $transaction->getCustomerId()->__toString());
    }

    /**
     * @test
     */
    public function it_register_new_return_transaction_and_assign_customer()
    {
        static::$kernel->boot();
        /** @var CustomerDetailsRepository $customerRepo */
        $customerRepo = static::$kernel->getContainer()->get('oloy.user.read_model.repository.customer_details');
        /** @var CustomerDetails $customer */
        $customer = $customerRepo->findOneByCriteria(['email' => 'user@oloy.com'], 1);
        $customer = reset($customer);
        $transactionsCount = $customer->getTransactionsCount();
        $transactionsAmount = $customer->getTransactionsAmount();

        $formData = [
            'revisedDocument' => '456',
            'transactionData' => [
                'documentNumber' => '456-return',
                'documentType' => 'return',
                'purchaseDate' => '2015-01-01',
                'purchasePlace' => 'wroclaw',
            ],
            'items' => [
                0 => [
                    'sku' => ['code' => '123'],
                    'name' => 'sku',
                    'quantity' => 1,
                    'grossValue' => -1,
                    'category' => 'test',
                    'maker' => 'company',
                ],
                1 => [
                    'sku' => ['code' => '1123'],
                    'name' => 'sku',
                    'quantity' => 2,
                    'grossValue' => -2,
                    'category' => 'test',
                    'maker' => 'company',
                ],
            ],
            'customerData' => [
                'name' => 'Jan Nowak',
                'email' => 'user@oloy.com',
                'nip' => 'aaa',
                'phone' => '123',
                'loyaltyCardNumber' => 'sa2222',
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
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200'.$response->getContent());
        static::$kernel->boot();
        $repo = static::$kernel->getContainer()->get('oloy.transaction.read_model.repository.transaction_details');
        /** @var TransactionDetails $transaction */
        $transaction = $repo->find($data['transactionId']);
        $this->assertInstanceOf(TransactionDetails::class, $transaction);
        $this->assertNotNull($transaction->getCustomerId());
        /** @var CustomerDetails $customer */
        $customer = $customerRepo->findOneByCriteria(['email' => 'user@oloy.com'], 1);
        $customer = reset($customer);
        $newTransactionsCount = $customer->getTransactionsCount();
        $newTransactionsAmount = $customer->getTransactionsAmount();
        $this->assertEquals($transactionsCount - 1, $newTransactionsCount);
        $this->assertEquals($transactionsAmount - 3, $newTransactionsAmount);
    }

    /**
     * @test
     */
    public function it_register_new_return_transaction_not_complete_and_assign_customer()
    {
        static::$kernel->boot();
        /** @var CustomerDetailsRepository $customerRepo */
        $customerRepo = static::$kernel->getContainer()->get('oloy.user.read_model.repository.customer_details');
        /** @var CustomerDetails $customer */
        $customer = $customerRepo->findOneByCriteria(['email' => 'user-temp@oloy.com'], 1);
        $customer = reset($customer);
        $transactionsCount = $customer->getTransactionsCount();
        $transactionsAmount = $customer->getTransactionsAmount();

        $formData = [
            'revisedDocument' => '789',
            'transactionData' => [
                'documentNumber' => '789-return',
                'documentType' => 'return',
                'purchaseDate' => '2015-01-01',
                'purchasePlace' => 'wroclaw',
            ],
            'items' => [
                0 => [
                    'sku' => ['code' => '123'],
                    'name' => 'sku',
                    'quantity' => 1,
                    'grossValue' => -1,
                    'category' => 'test',
                    'maker' => 'company',
                ],
            ],
            'customerData' => [
                'name' => 'Jan Nowak',
                'email' => 'user-temp@oloy.com',
                'nip' => 'aaa',
                'phone' => '123',
                'loyaltyCardNumber' => 'sa2222',
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
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200'.$response->getContent());
        static::$kernel->boot();
        $repo = static::$kernel->getContainer()->get('oloy.transaction.read_model.repository.transaction_details');
        /** @var TransactionDetails $transaction */
        $transaction = $repo->find($data['transactionId']);
        $this->assertInstanceOf(TransactionDetails::class, $transaction);
        $this->assertNotNull($transaction->getCustomerId());
        /** @var CustomerDetails $customer */
        $customer = $customerRepo->findOneByCriteria(['email' => 'user-temp@oloy.com'], 1);
        $customer = reset($customer);
        $newTransactionsCount = $customer->getTransactionsCount();
        $newTransactionsAmount = $customer->getTransactionsAmount();
        $this->assertEquals($transactionsCount, $newTransactionsCount);
        $this->assertEquals($transactionsAmount - 1, $newTransactionsAmount);
    }

    /**
     * @test
     */
    public function it_register_new_transaction_and_assign_customer_by_loyalty_card()
    {
        static::$kernel->boot();
        $settingsManager = $this->getMockBuilder(SettingsManager::class)->getMock();
        $settingsManager->method('getSettingByKey')->with($this->isType('string'))->will(
            $this->returnCallback(function ($arg) {
                if ($arg == 'customersIdentificationPriority') {
                    $entry = new JsonSettingEntry('customersIdentificationPriority');
                    $entry->setValue([
                        ['field' => 'loyaltyCardNumber'],
                    ]);

                    return $entry;
                }

                return null;
            }));

        $formData = [
            'transactionData' => [
                'documentNumber' => '123',
                'documentType' => 'sell',
                'purchaseDate' => '2015-01-01',
                'purchasePlace' => 'wroclaw',
            ],
            'items' => [
                0 => [
                    'sku' => ['code' => '123'],
                    'name' => 'sku',
                    'quantity' => 1,
                    'grossValue' => 1,
                    'category' => 'test',
                    'maker' => 'company',
                ],
                1 => [
                    'sku' => ['code' => '1123'],
                    'name' => 'sku',
                    'quantity' => 1,
                    'grossValue' => 11,
                    'category' => 'test',
                    'maker' => 'company',
                ],
            ],
            'customerData' => [
                'name' => 'Jan Nowak',
                'email' => 'user-temp@oloy.com',
                'nip' => 'aaa',
                'phone' => '123',
                'loyaltyCardNumber' => 'notfound',
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
        $client->getContainer()->set('ol.doctrine_settings.manager', $settingsManager);

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
        $repo = static::$kernel->getContainer()->get('oloy.transaction.read_model.repository.transaction_details');
        /** @var TransactionDetails $transaction */
        $transaction = $repo->find($data['transactionId']);
        $this->assertInstanceOf(TransactionDetails::class, $transaction);
        $this->assertNull($transaction->getCustomerId());
    }

    /**
     * @test
     */
    public function it_manually_assign_customer_to_transaction()
    {
        $formData = [
            'transactionDocumentNumber' => '888',
            'customerId' => LoadUserData::TEST_USER_ID,
        ];

        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/admin/transaction/customer/assign',
            [
                'assign' => $formData,
            ]
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200'.$response->getContent());
        static::$kernel->boot();
        $repo = static::$kernel->getContainer()->get('oloy.transaction.read_model.repository.transaction_details');
        /** @var TransactionDetails $transaction */
        $transaction = $repo->find($data['transactionId']);
        $this->assertInstanceOf(TransactionDetails::class, $transaction);
        $this->assertNotNull($transaction->getCustomerId());
        $this->assertEquals(LoadUserData::TEST_USER_ID, $transaction->getCustomerId()->__toString());
    }
}
