<?php

namespace OpenLoyalty\Bundle\EarningRuleBundle\Security;

use OpenLoyalty\Bundle\BaseAccessControlTest;
use OpenLoyalty\Bundle\EarningRuleBundle\DataFixtures\ORM\LoadEarningRuleData;

/**
 * Class EarningRuleControllerAccessTest.
 */
class EarningRuleControllerAccessTest extends BaseAccessControlTest
{
    /**
     * @test
     */
    public function only_admin_and_seller_should_have_access_to_all_earning_rule_list()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'not_status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/earningRule');
    }

    /**
     * @test
     */
    public function only_admin_can_create_earning_rule()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/earningRule', [], 'POST');
    }

    /**
     * @test
     */
    public function only_admin_can_edit_earning_rule()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/earningRule/'.LoadEarningRuleData::EVENT_RULE_ID, [], 'PUT');
    }

    /**
     * @test
     */
    public function only_admin_and_seller_can_view_earning_rule()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'not_status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/earningRule/'.LoadEarningRuleData::EVENT_RULE_ID);
    }
}
