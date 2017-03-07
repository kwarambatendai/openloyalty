<?php

namespace OpenLoyalty\Domain\Customer\Command;

use OpenLoyalty\Domain\Customer\CustomerId;

/**
 * Class UpdateCustomerCompanyDetails.
 */
class UpdateCustomerCompanyDetails extends CustomerCommand
{
    protected $companyData;

    /**
     * UpdateCompanyDetails constructor.
     *
     * @param CustomerId $customerId
     * @param $companyData
     */
    public function __construct(CustomerId $customerId, $companyData)
    {
        parent::__construct($customerId);
        $this->companyData = $companyData;
    }

    /**
     * @return mixed
     */
    public function getCompanyData()
    {
        return $this->companyData;
    }
}
