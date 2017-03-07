<?php

namespace OpenLoyalty\Bundle\PosBundle\Security;

use OpenLoyalty\Bundle\BaseAccessControlTest;
use OpenLoyalty\Bundle\PosBundle\DataFixtures\ORM\LoadPosData;

/**
 * Class PosControllerAccessTest.
 */
class PosControllerAccessTest extends BaseAccessControlTest
{
    /**
     * @test
     */
    public function only_admin_and_seller_should_have_access_to_all_pos_list()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'not_status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/pos');
    }

    /**
     * @test
     */
    public function only_admin_can_edit_pos()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/pos/'.LoadPosData::POS_ID, [], 'PUT');
    }

    /**
     * @test
     */
    public function only_admin_can_create_pos()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/pos', [], 'POST');
    }

    /**
     * @test
     */
    public function only_admin_and_seller_can_view_pos()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'not_status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/pos/'.LoadPosData::POS_ID);
    }
}