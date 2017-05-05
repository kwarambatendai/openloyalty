<?php

namespace OpenLoyalty\Bundle\PointsBundle\Security;

use OpenLoyalty\Bundle\BaseAccessControlTest;

/**
 * Class CustomerPointsTransferControllerAccessTest.
 */
class CustomerPointsTransferControllerAccessTest extends BaseAccessControlTest
{
    /**
     * @test
     */
    public function only_customer_should_have_access_to_his_transfers()
    {
        $clients = [
            ['client' => $this->getCustomerClient(), 'not_status' => 403, 'name' => 'customer'],
            ['client' => $this->getSellerClient(), 'status' => 403, 'name' => 'seller'],
            ['client' => $this->getAdminClient(), 'status' => 403, 'name' => 'admin'],
        ];

        $this->checkClients($clients, '/api/customer/points/transfer');
    }
}
