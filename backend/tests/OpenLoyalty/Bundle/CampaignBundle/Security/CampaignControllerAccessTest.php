<?php

namespace OpenLoyalty\Bundle\CampaignBundle\Security;

use OpenLoyalty\Bundle\BaseAccessControlTest;
use OpenLoyalty\Bundle\CampaignBundle\DataFixtures\ORM\LoadCampaignData;
use OpenLoyalty\Bundle\CampaignBundle\Service\CampaignProvider;
use OpenLoyalty\Bundle\UserBundle\DataFixtures\ORM\LoadUserData;
use OpenLoyalty\Domain\Campaign\Campaign;

/**
 * Class CampaignControllerAccessTest.
 */
class CampaignControllerAccessTest extends BaseAccessControlTest
{
    /**
     * @test
     */
    public function only_admin_should_have_access_to_all_campaigns_list()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/campaign');
    }

    /**
     * @test
     */
    public function only_admin_can_create_campaign()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/campaign', [], 'POST');
    }

    /**
     * @test
     */
    public function only_admin_can_edit_campaign()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/campaign/'.LoadCampaignData::CAMPAIGN_ID, [], 'PUT');
    }

    /**
     * @test
     */
    public function admin_can_get_all_campaigns_but_customer_only_visible_for_him()
    {
        static::bootKernel();
        $provider = $this->getMockBuilder(CampaignProvider::class)->disableOriginalConstructor()->getMock();
        $provider->method('visibleForCustomers')->with($this->isInstanceOf(Campaign::class))
            ->will($this->returnCallback(function (Campaign $campaign) {
                if ($campaign->getCampaignId()->__toString() == LoadCampaignData::CAMPAIGN_ID) {
                    return [LoadUserData::USER_USER_ID];
                }
                if ($campaign->getCampaignId()->__toString() == LoadCampaignData::CAMPAIGN2_ID) {
                    return [];
                }
            }));


        $customerClient = $this->getCustomerClient();
        static::$kernel->getContainer()->set('oloy.campaign.campaign_provider', $provider);
        $customerClient->getContainer()->set('oloy.campaign.campaign_provider', $provider);
        $customerClient->request('GET', '/api/campaign/'.LoadCampaignData::CAMPAIGN_ID);
        $this->assertTrue(
            403 != $customerClient->getResponse()->getStatusCode(),
            '403 should not be returned'
        );

        $clients = [
            ['client' => $this->getSellerClient(), 'not_status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/campaign/'.LoadCampaignData::CAMPAIGN_ID);

        $clients = [
            ['client' => $this->getSellerClient(), 'not_status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/campaign/'.LoadCampaignData::CAMPAIGN2_ID);

        $customerClient = $this->getCustomerClient();
        static::$kernel->getContainer()->set('oloy.campaign.campaign_provider', $provider);
        $customerClient->getContainer()->set('oloy.campaign.campaign_provider', $provider);
        $customerClient->request('GET', '/api/campaign/'.LoadCampaignData::CAMPAIGN2_ID);
        $this->assertTrue(
            403 == $customerClient->getResponse()->getStatusCode(),
            '403 should be returned'
        );
    }

    /**
     * @test
     */
    public function only_admin_can_get_campaign_customers()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/campaign/'.LoadCampaignData::CAMPAIGN_ID.'/customers/visible');
    }
}
