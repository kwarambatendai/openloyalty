<?php

namespace OpenLoyalty\Bundle\UserBundle\Security;

use OpenLoyalty\Bundle\BaseAccessControlTest;

/**
 * Class CustomerControllerAccessTest.
 */
class CustomerControllerAccessTest extends BaseAccessControlTest
{
    /**
     * @test
     */
    public function
    only_admin_and_seller_has_access_to_customers_list()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'not_status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'not_status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/customer');
    }
}
