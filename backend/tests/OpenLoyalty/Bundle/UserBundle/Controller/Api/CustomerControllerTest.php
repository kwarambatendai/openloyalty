<?php

namespace OpenLoyalty\Bundle\UserBundle\Controller\Api;

use OpenLoyalty\Bundle\BaseApiTest;
use OpenLoyalty\Bundle\LevelBundle\DataFixtures\ORM\LoadLevelData;
use OpenLoyalty\Bundle\UserBundle\DataFixtures\ORM\LoadUserData;
use OpenLoyalty\Domain\Customer\Command\CustomerCommandHandlerTest;
use OpenLoyalty\Domain\Customer\PosId;

/**
 * Class CustomerControllerTest.
 */
class CustomerControllerTest extends BaseApiTest
{
    /**
     * @test
     */
    public function it_allows_to_register_new_customer()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/customer/register',
            [
                'customer' => [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'email' => 'john@doe.com',
                    'gender' => 'male',
                    'birthDate' => '1990-01-01',
                    'address' => [
                        'street' => 'Bagno',
                        'address1' => '12',
                        'postal' => '00-800',
                        'city' => 'Warszawa',
                        'country' => 'PL',
                        'province' => 'mazowieckie',
                    ],
                    'agreement1' => true,
                    'agreement2' => true,
                    'loyaltyCardNumber' => '0000000011',
                ],
            ]
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200'.$response->getContent());
        $this->assertArrayHasKey('customerId', $data);
        $this->assertArrayHasKey('email', $data);
    }

    /**
     * @test
     */
    public function it_allows_to_register_new_customer_by_seller()
    {
        $client = $this->createAuthenticatedClient('john@doe.com', 'open', 'seller');
        $client->request(
            'POST',
            '/api/customer/register',
            [
                'customer' => [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'email' => 'john33@doe.com',
                    'gender' => 'male',
                    'birthDate' => '1990-01-01',
                    'levelId' => LoadLevelData::LEVEL3_ID,
                    'address' => [
                        'street' => 'Bagno',
                        'address1' => '12',
                        'postal' => '00-800',
                        'city' => 'Warszawa',
                        'country' => 'PL',
                        'province' => 'mazowieckie',
                    ],
                    'agreement1' => true,
                    'agreement2' => true,
                ],
            ]
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200'.$response->getContent());
        $this->assertArrayHasKey('customerId', $data);
        $this->assertArrayHasKey('email', $data);
    }

    /**
     * @test
     */
    public function it_allows_to_register_new_customer_by_himself()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/customer/self_register',
            [
                'customer' => [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'email' => 'john22@doe.com',
                    'gender' => 'male',
                    'birthDate' => '1990-01-01',
                    'address' => [
                        'street' => 'Bagno',
                        'address1' => '12',
                        'postal' => '00-800',
                        'city' => 'Warszawa',
                        'country' => 'PL',
                        'province' => 'mazowieckie',
                    ],
                    'agreement1' => true,
                    'agreement2' => true,
                    'plainPassword' => 'OpenLoyalty123!',
                ],
            ]
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200'.$response->getContent());
        $this->assertArrayHasKey('customerId', $data);
        $this->assertArrayHasKey('email', $data);
    }

    /**
     * @test
     */
    public function it_allows_to_register_new_customer_with_only_required_data_and_some_data_in_address()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/customer/register',
            [
                'customer' => [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'email' => 'john@loyalty.com',
                    'agreement1' => true,
                    'address' => [
                        'street' => 'Bagno',
                        'postal' => '00-800',
                        'city' => 'Warszawa',
                    ],
                ],
            ]
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $this->assertArrayHasKey('customerId', $data);
        $this->assertArrayHasKey('email', $data);
    }

    /**
     * @test
     */
    public function it_properly_validates_address()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/customer/register',
            [
                'customer' => [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'email' => 'john3@doe.com',
                    'gender' => 'male',
                    'birthDate' => '1990-01-01',
                    'agreement1' => true,
                    'address' => [
                        'street' => 'Bagno',
                        'address1' => '12',
                        'postal' => '00-800',
                        'country' => 'PL',
                        'province' => 'mazowieckie',
                    ],
                    'loyaltyCardNumber' => '0000000011',
                ],
            ]
        );

        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'Response should have status 400');
    }

    /**
     * @test
     */
    public function it_allows_to_register_new_customer_without_certain_fields()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/customer/register',
            [
                'customer' => [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'email' => 'john2@doe.com',
                    'agreement1' => true,
                ],
            ]
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $this->assertArrayHasKey('customerId', $data);
        $this->assertArrayHasKey('email', $data);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_register_new_customer_with_the_same_email()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/customer/register',
            [
                'customer' => [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'email' => 'john@doe.com',
                    'agreement1' => true,
                    'gender' => 'male',
                    'birthDate' => '1990-01-01',
                    'address' => [
                        'street' => 'Bagno',
                        'address1' => '12',
                        'postal' => '00-800',
                        'city' => 'Warszawa',
                        'country' => 'PL',
                        'province' => 'mazowieckie',
                    ],
                    'loyaltyCardNumber' => '000000000',
                ],
            ]
        );

        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'Response should have status 400');
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_register_new_customer_with_the_same_loyalty_card_number()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/customer/register',
            [
                'customer' => [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'email' => 'john2@doe.com',
                    'agreement1' => true,
                    'gender' => 'male',
                    'birthDate' => '1990-01-01',
                    'address' => [
                        'street' => 'Bagno',
                        'address1' => '12',
                        'postal' => '00-800',
                        'city' => 'Warszawa',
                        'country' => 'PL',
                        'province' => 'mazowieckie',
                    ],
                    'loyaltyCardNumber' => '0000000011',
                ],
            ]
        );

        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'Response should have status 400');
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_register_new_customer_with_the_same_phone()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/customer/register',
            [
                'customer' => [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'email' => 'john3@doe.com',
                    'agreement1' => true,
                    'gender' => 'male',
                    'birthDate' => '1990-01-01',
                    'address' => [
                        'street' => 'Bagno',
                        'address1' => '12',
                        'postal' => '00-800',
                        'city' => 'Warszawa',
                        'country' => 'PL',
                        'province' => 'mazowieckie',
                    ],
                    'phone' => '11111',
                    'loyaltyCardNumber' => '0000000011',
                ],
            ]
        );

        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'Response should have status 400');
    }

    /**
     * @test
     */
    public function it_allows_to_edit_customer_details()
    {
        $client = $this->createAuthenticatedClient();
        $customerData = CustomerCommandHandlerTest::getCustomerData();
        $tmp = new \DateTime();
        $tmp->setTimestamp($customerData['birthDate']);
        $customerData['birthDate'] = $tmp->format('Y-m-d');
        unset($customerData['createdAt']);
        unset($customerData['updatedAt']);
        $customerData['firstName'] = 'Jane';
        $customerData['address']['street'] = 'Prosta';
        $client->request(
            'PUT',
            '/api/customer/'.LoadUserData::TEST_USER_ID,
            [
                'customer' => $customerData,
            ]
        );

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');

        self::$kernel->boot();

        $client = $this->createAuthenticatedClient();
        $client->request(
            'GET',
            '/api/customer/'.LoadUserData::TEST_USER_ID
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $this->assertEquals('Jane', $data['firstName']);
    }

    /**
     * @test
     */
    public function it_allows_to_remove_customer_company()
    {
        $customerData = CustomerCommandHandlerTest::getCustomerData();
        $tmp = new \DateTime();
        $tmp->setTimestamp($customerData['birthDate']);
        $customerData['birthDate'] = $tmp->format('Y-m-d');
        $customerData['phone'] = '111222333';
        unset($customerData['createdAt']);
        unset($customerData['updatedAt']);
        $customerData['company'] = null;
        $client = $this->createAuthenticatedClient();
        $client->request(
            'PUT',
            '/api/customer/'.LoadUserData::TEST_USER_ID,
            [
                'customer' => $customerData,
            ]
        );

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');

        self::$kernel->boot();

        $client = $this->createAuthenticatedClient();
        $client->request(
            'GET',
            '/api/customer/'.LoadUserData::TEST_USER_ID
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $this->assertTrue(empty($data['company']));
    }

    /**
     * @test
     */
    public function it_allows_to_get_customer_details()
    {
        self::$kernel->boot();
        $user = self::$kernel->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository('OpenLoyaltyUserBundle:User')->findOneBy(['email' => 'user@oloy.com']);
        $id = $user->getId();

        $client = $this->createAuthenticatedClient();
        $client->request(
            'GET',
            '/api/customer/'.$id
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $this->assertEquals('John', $data['firstName']);
        $this->assertEquals('male', $data['gender']);
    }

    /**
     * @test
     */
    public function it_allows_to_get_customers_list()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'GET',
            '/api/customer'
        );

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
    }

    /**
     * @test
     */
    public function it_allows_to_get_customers_list_filtered_by_first_name()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'GET',
            '/api/customer?firstName=John'
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $this->assertTrue(count($data['customers']) > 0);
        $this->assertEquals('John', $data['customers'][0]['firstName']);
    }

    /**
     * @test
     */
    public function it_allows_to_get_customers_list_filtered_by_email()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'GET',
            '/api/customer?email=user@oloy.com'
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $this->assertCount(1, $data['customers']);
        $this->assertEquals('John', $data['customers'][0]['firstName']);
        $this->assertEquals('user@oloy.com', $data['customers'][0]['email']);
    }

    /**
     * @test
     */
    public function it_allows_to_assing_pos_to_customer()
    {
        $client = $this->createAuthenticatedClient();
        $posId = new PosId('00000000-0000-0000-0000-000000000011');

        $client->request(
            'POST',
            '/api/customer/'.LoadUserData::TEST_USER_ID.'/pos',
            [
                'posId' => $posId->__toString(),
            ]
        );

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');

        self::$kernel->boot();

        $client = $this->createAuthenticatedClient();
        $client->request(
            'GET',
            '/api/customer/'.LoadUserData::TEST_USER_ID
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $this->assertArrayHasKey('posId', $data);
        $this->assertEquals($posId->__toString(), $data['posId'], json_encode($data));
    }

    /**
     * @test
     */
    public function it_does_not_allow_registration_with_wrong_referral_customer_mail()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/customer/register',
            [
                'customer' => [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'email' => 'johhhn@doe.com',
                    'agreement1' => true,
                    'referral_customer_email' => 'referral_customer_mail@test.com',
                ],
            ]
        );

        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'Response should have status 400');

        $expectedResponseArray = [
            'form' => [
                'children' => [
                    'firstName' => [],
                    'lastName' => [],
                    'gender' => [],
                    'email' => [],
                    'phone' => [],
                    'birthDate' => [],
                    'createdAt' => [],
                    'address' => [
                        'children' => [
                            'street' => [],
                            'address1' => [],
                            'address2' => [],
                            'postal' => [],
                            'city' => [],
                            'province' => [],
                            'country' => [],
                        ],
                    ],
                    'company' => [
                        'children' => [
                            'name' => [],
                            'nip' => [],
                        ],
                    ],
                    'loyaltyCardNumber' => [],
                    'agreement1' => [],
                    'agreement2' => [],
                    'agreement3' => [],
                    'referral_customer_email' => [
                        'errors' => ["Referral customer email doesn't exist"],
                    ],
                    'levelId' => [],
                    'posId' => [],
                ],
            ],
          'errors' => [],
        ];

        $data = json_decode($response->getContent(), true);
        $this->assertEquals($expectedResponseArray, $data);
    }

    /**
     * @test
     */
    public function it_allow_registration_with_proper_referral_email_and_add_points_to_referral_customer()
    {
        $client = $this->createAuthenticatedClient();

        /** Get referral customer points*/
        $points = $this->getCustomerPoints($client, LoadUserData::USER_USER_ID);

        /* Create new customer with referral email */
        $client->request(
            'POST',
            '/api/customer/register',
            [
                'customer' => [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'email' => 'johhhn@doe.com',
                    'agreement1' => true,
                    'referral_customer_email' => LoadUserData::USER_USERNAME,
                ],
            ]
        );
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');

        /** Test if referral customer has 75 points more after customer referral event */
        $expectedPoints = $points + 75;

        $customerPoints = $this->getCustomerPoints($client, LoadUserData::USER_USER_ID);
        $this->assertEquals($expectedPoints, $customerPoints);
    }

    /**
     * @test
     */
    public function it_allow_registration_with_empty_referral_email()
    {
        $client = $this->createAuthenticatedClient();

        /* Create new customer with referral email */
        $client->request(
            'POST',
            '/api/customer/register',
            [
                'customer' => [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'email' => 'johhhnn@doe.com',
                    'agreement1' => true,
                    'referral_customer_email' => '',
                ],
            ]
        );
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
    }

    /**
     * @test
     */
    public function it_does_not_allow_self_registration_with_wrong_referral_email()
    {
        $client = $this->createAuthenticatedClient();

        $client->request(
            'POST',
            '/api/customer/self_register',
            [
                'customer' => [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'email' => 'self_register@doe.com',
                    'gender' => 'male',
                    'birthDate' => '1990-01-01',
                    'address' => [
                        'street' => 'Bagno',
                        'address1' => '12',
                        'postal' => '00-800',
                        'city' => 'Warszawa',
                        'country' => 'PL',
                        'province' => 'mazowieckie',
                    ],
                    'agreement1' => true,
                    'agreement2' => true,
                    'plainPassword' => 'OpenLoyalty123!',
                    'referral_customer_email' => 'wrong@test.email.com',
                ],
            ]
        );

        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'Response should have status 400');

        $expectedResponseArray = [
            'form' => [
                'children' => [
                    'firstName' => [],
                    'lastName' => [],
                    'gender' => [],
                    'email' => [],
                    'phone' => [],
                    'birthDate' => [],
                    'createdAt' => [],
                    'address' => [
                        'children' => [
                            'street' => [],
                            'address1' => [],
                            'address2' => [],
                            'postal' => [],
                            'city' => [],
                            'province' => [],
                            'country' => [],
                        ],
                    ],
                    'company' => [
                        'children' => [
                            'name' => [],
                            'nip' => [],
                        ],
                    ],
                    'loyaltyCardNumber' => [],
                    'agreement1' => [],
                    'agreement2' => [],
                    'agreement3' => [],
                    'referral_customer_email' => [
                        'errors' => ["Referral customer email doesn't exist"],
                    ],
                    'plainPassword' => [],
                ],
            ],
            'errors' => [],
        ];

        $data = json_decode($response->getContent(), true);
        $this->assertEquals($expectedResponseArray, $data);
    }

    /**
     * @test
     */
    public function it_allow_self_registration_with_proper_referral_email()
    {
        $client = $this->createAuthenticatedClient();

        /* Create new customer with referral email */
        $client->request(
            'POST',
            '/api/customer/self_register',
            [
                'customer' => [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'email' => 'self_register@doe.com',
                    'agreement1' => true,
                    'plainPassword' => 'OpenLoyalty123!',
                    'referral_customer_email' => LoadUserData::USER_USERNAME,
                ],
            ]
        );
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
    }

    /**
     * @test
     */
    public function it_allow_self_registration_with_empty_referral_email()
    {
        $client = $this->createAuthenticatedClient();

        /* Create new customer with referral email */
        $client->request(
            'POST',
            '/api/customer/self_register',
            [
                'customer' => [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'email' => 'self_register2@doe.com',
                    'agreement1' => true,
                    'plainPassword' => 'OpenLoyalty123!',
                    'referral_customer_email' => '',
                ],
            ]
        );
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
    }

    /**
     * @test
     * @depends it_allow_self_registration_with_proper_referral_email
     */
    public function it_add_points_to_referral_customer_after_customer_activation()
    {
        $client = $this->createAuthenticatedClient();
        $customerEmail = 'self_register@doe.com';

        //Get referral customer points amount
        $points = $this->getCustomerPoints($client, LoadUserData::USER_USER_ID);

        //Activate customer

        $activateToken = $this->getActivateTokenForCustomer($customerEmail);

        $client->request(
            'POST',
            '/api/customer/activate/'.$activateToken
        );
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');

        // Test if referral customer has 75 points more after customer referral event
        $expectedPoints = $points + 75;
        $points = $this->getCustomerPoints($client, LoadUserData::USER_USER_ID);
        $this->assertEquals($expectedPoints, $points);
    }

    /**
     * @test
     */
    public function it_add_points_to_customer_after_register_on_agreement2_checked()
    {
        $client = $this->createAuthenticatedClient();

        /* Create new customer with marketing agreement */
        $client->request(
            'POST',
            '/api/customer/register',
            [
                'customer' => [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'email' => 'marketing@doe.com',
                    'agreement1' => true,
                    'agreement2' => true,
                ],
            ]
        );
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');

        $responseBody = json_decode($response->getContent());
        $points = $this->getCustomerPoints($client, $responseBody->customerId);
        $this->assertEquals(85, $points);

        //Test newsletter subscribe flag
        $customer = $this->getCustomerEntity($responseBody->customerId);
        $this->assertTrue($customer->getNewsletterUsedFlag());
    }

    /**
     * @test
     */
    public function it_add_points_to_customer_after_self_register_customer_activation_on_agreement2_checked()
    {
        $client = $this->createAuthenticatedClient();
        $customerEmail = 'marketing_self@doe.com';

        /* Create new customer with marketing agreement */
        $client->request(
            'POST',
            '/api/customer/self_register',
            [
                'customer' => [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'email' => $customerEmail,
                    'agreement1' => true,
                    'agreement2' => true,
                    'plainPassword' => 'OpenLoyalty123!',
                ],
            ]
        );
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');

        $customerId = json_decode($response->getContent())->customerId;
        $points = $this->getCustomerPoints($client, $customerId);
        $this->assertEquals(0, $points);

        //Test newsletter subscribe flag
        $customer = $this->getCustomerEntity($customerId);
        $this->assertFalse($customer->getNewsletterUsedFlag());

        //Activate customer
        $activateToken = $this->getActivateTokenForCustomer($customerEmail);
        $client->request(
            'POST',
            '/api/customer/activate/'.$activateToken
        );
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');

        //Check points from newsletter subscription event
        $points = $this->getCustomerPoints($client, $customerId);
        $this->assertEquals(85, $points);

        //Test newsletter subscribe flag
        $customer = $this->getCustomerEntity($customerId);
        $this->assertTrue($customer->getNewsletterUsedFlag());
    }

    /**
     * @test
     */
    public function if_add_points_to_customer_after_account_edit_and_agreement2_check()
    {
        $client = $this->createAuthenticatedClient();
        $customerId = LoadUserData::TEST_USER_ID;
        $points = $this->getCustomerPoints($client, $customerId);

        //Update customer data with checked agreement2
        $client->request(
            'GET',
            '/api/customer/'.$customerId
        );
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $customerData = json_decode($response->getContent(), true);
        $this->assertEquals(false, $customerData['agreement2'], 'Agreement2 should be false');

        //Test newsletter subscribe flag
        $customer = $this->getCustomerEntity(LoadUserData::TEST_USER_ID);
        $this->assertFalse($customer->getNewsletterUsedFlag());

        $client->request(
            'PUT',
            '/api/customer/'.$customerId,
            [
                'customer' => [
                    'firstName' => $customerData['firstName'],
                    'lastName' => $customerData['lastName'],
                    'email' => $customerData['email'],
                    'agreement1' => true,
                    'agreement2' => true,
                ],
            ]
        );
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');

        //Check customer points
        $expectedPoints = $points + 85;
        $this->assertEquals($expectedPoints, $this->getCustomerPoints($client, $customerId));

        //Test newsletter subscribe flag
        $customer = $this->getCustomerEntity(LoadUserData::TEST_USER_ID);
        $this->assertTrue($customer->getNewsletterUsedFlag());
    }

    /**
     * @test
     * @depends if_add_points_to_customer_after_account_edit_and_agreement2_check
     */
    public function it_dont_add_points_after_customer_send_false_agreement2()
    {
        $client = $this->createAuthenticatedClient();
        $customerId = LoadUserData::TEST_USER_ID;
        $points = $this->getCustomerPoints($client, $customerId);

        //Update customer data with checked agreement2
        $client->request(
            'GET',
            '/api/customer/'.$customerId
        );
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $customerData = json_decode($response->getContent(), true);
        $this->assertEquals(true, $customerData['agreement2'], 'Agreement2 should be true');

        $client->request(
            'PUT',
            '/api/customer/'.$customerId,
            [
                'customer' => [
                    'firstName' => $customerData['firstName'],
                    'lastName' => $customerData['lastName'],
                    'email' => $customerData['email'],
                    'agreement1' => true,
                    'agreement2' => false,
                ],
            ]
        );
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');

        //Check customer points
        $this->assertEquals($points, $this->getCustomerPoints($client, $customerId));

        //Test newsletter subscribe flag
        $customer = $this->getCustomerEntity($customerId);
        $this->assertTrue($customer->getNewsletterUsedFlag());
    }

    /**
     * @test
     * @depends it_dont_add_points_after_customer_send_false_agreement2
     */
    public function it_dont_add_points_after_2nd_attempt_to_newsletter_subscription()
    {
        $client = $this->createAuthenticatedClient();
        $customerId = LoadUserData::TEST_USER_ID;
        $points = $this->getCustomerPoints($client, $customerId);

        //Test newsletter subscribe flag
        $customer = $this->getCustomerEntity($customerId);
        $this->assertTrue($customer->getNewsletterUsedFlag());

        //Update customer data with checked agreement2
        $client->request(
            'GET',
            '/api/customer/'.$customerId
        );
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');
        $customerData = json_decode($response->getContent(), true);
        $this->assertEquals(false, $customerData['agreement2'], 'Agreement2 should be false');

        $client->request(
            'PUT',
            '/api/customer/'.$customerId,
            [
                'customer' => [
                    'firstName' => $customerData['firstName'],
                    'lastName' => $customerData['lastName'],
                    'email' => $customerData['email'],
                    'agreement1' => true,
                    'agreement2' => true,
                ],
            ]
        );
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'Response should have status 200');

        //Check customer points
        $this->assertEquals($points, $this->getCustomerPoints($client, $customerId));

        //Test newsletter subscribe flag
        $customer = $this->getCustomerEntity($customerId);
        $this->assertTrue($customer->getNewsletterUsedFlag());
    }
}
