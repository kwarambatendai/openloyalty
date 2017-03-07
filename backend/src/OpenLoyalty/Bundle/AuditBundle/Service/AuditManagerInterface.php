<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\AuditBundle\Service;

use OpenLoyalty\Domain\Customer\CustomerId;

/**
 * Interface AuditManagerInterface.
 */
interface AuditManagerInterface
{
    const VIEW_CUSTOMER_EVENT_TYPE = 'ViewCustomer';

    public function auditCustomerEvent($eventType, CustomerId $customerId, array $data);
}
