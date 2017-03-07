<?php

namespace OpenLoyalty\Bundle\Transaction\Security;

use OpenLoyalty\Bundle\BaseAccessControlTest;
use OpenLoyalty\Bundle\TransactionBundle\DataFixtures\ORM\LoadTransactionData;

/**
 * Class TransactionControllerAccessTest.
 */
class TransactionControllerAccessTest extends BaseAccessControlTest
{
    /**
     * @test
     */
    public function customer_admin_and_seller_should_have_access_to_list()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'not_status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'not_status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/transaction');
    }

    /**
     * @test
     */
    public function customer_admin_and_seller_should_have_access_to_item_labels()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'not_status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'not_status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/transaction/item/labels');
    }

    /**
     * @test
     */
    public function only_admin_and_customer_should_have_access_to_transaction_assigned_to_this_customer()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'not_status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'not_status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/transaction/'.LoadTransactionData::TRANSACTION3_ID);
    }

    /**
     * @test
     */
    public function only_admin_can_register_transaction()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/transaction', [], 'POST');
    }
}