<?php

namespace OpenLoyalty\Domain\Customer\Command;

use OpenLoyalty\Domain\Customer\CustomerId;

/**
 * Class RegisterCustomer.
 */
class RegisterCustomer extends CustomerCommand
{
    protected $customerData;

    /**
     * RegisterCustomerCommand.
     *
     * @param CustomerId $customerId
     * @param $customerData
     */
    public function __construct(CustomerId $customerId, $customerData)
    {
        parent::__construct($customerId);
        if (!isset($customerData['createdAt'])) {
            $customerData['createdAt'] = (new \DateTime())->getTimestamp();
        }
        $this->customerData = $customerData;
    }

    /**
     * @return mixed
     */
    public function getCustomerData()
    {
        return $this->customerData;
    }
}
