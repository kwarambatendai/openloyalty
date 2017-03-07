<?php

namespace OpenLoyalty\Bundle\CampaignBundle\Security;

use OpenLoyalty\Bundle\BaseAccessControlTest;
use OpenLoyalty\Bundle\CampaignBundle\DataFixtures\ORM\LoadCampaignData;

/**
 * Class CustomerCampaignsControllerAccessTest.
 */
class CustomerCampaignsControllerAccessTest extends BaseAccessControlTest
{
    /**
     * @test
     */
    public function only_customer_has_access_to_this_controller_methods()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'not_status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/customer/campaign/available');
        $this->checkClients($clients, '/api/customer/campaign/bought');
        $this->checkClients($clients, '/api/customer/campaign/'.LoadCampaignData::CAMPAIGN_ID.'/buy', [], 'POST');
    }
}
