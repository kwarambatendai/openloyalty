<?php

namespace OpenLoyalty\Bundle\SegmentBundle\Security;

use OpenLoyalty\Bundle\BaseAccessControlTest;
use OpenLoyalty\Bundle\SegmentBundle\DataFixtures\ORM\LoadSegmentData;

/**
 * Class SegmentControllerAccessTest.
 */
class SegmentControllerAccessTest extends BaseAccessControlTest
{
    /**
     * @test
     */
    public function only_admin_should_have_access_to_all_segment_list()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/segment');
    }

    /**
     * @test
     */
    public function only_admin_should_have_access_to_segment_customers()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/segment/'.LoadSegmentData::SEGMENT2_ID.'/customers');
    }

    /**
     * @test
     */
    public function only_admin_can_edit_segment()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/segment/'.LoadSegmentData::SEGMENT2_ID, [], 'PUT');
    }

    /**
     * @test
     */
    public function only_admin_can_create_segment()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/segment', [], 'POST');
    }

    /**
     * @test
     */
    public function only_admin_can_activate_segment()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/segment/'.LoadSegmentData::SEGMENT2_ID.'/activate', [], 'POST');
    }

    /**
     * @test
     */
    public function only_admin_can_deactivate_segment()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/segment/'.LoadSegmentData::SEGMENT2_ID.'/deactivate', [], 'POST');
    }

    /**
     * @test
     */
    public function only_admin_can_delete_segment()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/segment/'.LoadSegmentData::SEGMENT2_ID, [], 'DELETE');
    }

    /**
     * @test
     */
    public function only_admin_can_view_segment()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/segment/'.LoadSegmentData::SEGMENT_ID);
    }
}
