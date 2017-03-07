<?php

namespace OpenLoyalty\Bundle\UserBundle\Security;

use OpenLoyalty\Bundle\BaseAccessControlTest;
use OpenLoyalty\Bundle\UserBundle\DataFixtures\ORM\LoadUserData;

/**
 * Class SellerControllerAccessTest.
 */
class SellerControllerAccessTest extends BaseAccessControlTest
{
    /**
     * @test
     */
    public function only_admin_has_access_to_sellers_list()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/seller');
    }

    /**
     * @test
     */
    public function only_admin_has_access_to_seller_registration()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/seller/register', [], 'POST');
    }

    /**
     * @test
     */
    public function only_admin_has_access_to_seller_activation()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/seller/'.LoadUserData::TEST_SELLER_ID.'/activate', [], 'POST');
    }

    /**
     * @test
     */
    public function only_admin_has_access_to_seller_edit()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/seller/'.LoadUserData::TEST_SELLER2_ID, [], 'PUT');
    }

    /**
     * @test
     */
    public function seller_can_view_himself()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'not_status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/seller/'.LoadUserData::TEST_SELLER_ID);
    }

    /**
     * @test
     */
    public function seller_cannot_view_other_seller()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/seller/'.LoadUserData::TEST_SELLER2_ID);
    }
    /**
     * @test
     */
    public function only_admin_has_access_to_seller_deactivation()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/seller/'.LoadUserData::TEST_SELLER2_ID.'/deactivate', [], 'POST');
    }

    /**
     * @test
     */
    public function only_admin_has_access_to_seller_delete()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/seller/'.LoadUserData::TEST_SELLER2_ID.'/delete', [], 'POST');
    }
}
