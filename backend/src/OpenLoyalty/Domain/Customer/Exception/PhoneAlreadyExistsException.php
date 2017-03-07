<?php

namespace OpenLoyalty\Domain\Customer\Exception;

/**
 * Class PhoneAlreadyExistsException.
 */
class PhoneAlreadyExistsException extends CustomerValidationException
{
    protected $message = 'customer with such phone already exists';
}
