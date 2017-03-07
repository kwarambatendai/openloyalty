<?php

namespace OpenLoyalty\Domain\Customer\Command;

use OpenLoyalty\Domain\Customer\CustomerId;

/**
 * Class UpdateCustomerAddress.
 */
class UpdateCustomerAddress extends CustomerCommand
{
    protected $addressData;

    /**
     * UpdateCustomerAddress constructor.
     *
     * @param CustomerId $customerId
     * @param $addressData
     */
    public function __construct(CustomerId $customerId, $addressData)
    {
        parent::__construct($customerId);
        $this->addressData = $addressData;
    }

    /**
     * @return mixed
     */
    public function getAddressData()
    {
        return $this->addressData;
    }
}
