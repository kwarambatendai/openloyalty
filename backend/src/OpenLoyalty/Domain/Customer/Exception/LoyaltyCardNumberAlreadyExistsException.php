<?php

namespace OpenLoyalty\Domain\Customer\Exception;

/**
 * Class LoyaltyCardNumberAlreadyExistsException.
 */
class LoyaltyCardNumberAlreadyExistsException extends CustomerValidationException
{
    protected $message = 'customer with such loyalty card number already exists';
}
