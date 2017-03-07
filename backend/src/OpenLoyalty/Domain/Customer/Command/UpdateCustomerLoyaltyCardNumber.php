<?php

namespace OpenLoyalty\Domain\Customer\Command;

use OpenLoyalty\Domain\Customer\CustomerId;

/**
 * Class UpdateCustomerLoyaltyCardNumber.
 */
class UpdateCustomerLoyaltyCardNumber extends CustomerCommand
{
    /**
     * @var string
     */
    protected $cardNumber;

    /**
     * UpdateCustomerLoyaltyCardNumber constructor.
     *
     * @param CustomerId $customerId
     * @param $cardNumber
     */
    public function __construct(CustomerId $customerId, $cardNumber)
    {
        parent::__construct($customerId);
        $this->cardNumber = $cardNumber;
    }

    /**
     * @return string
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }
}
