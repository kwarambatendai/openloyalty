<?php

namespace OpenLoyalty\Domain\Customer\Exception;

/**
 * Class EmailAlreadyExistsException.
 */
class EmailAlreadyExistsException extends CustomerValidationException
{
    protected $message = 'customer with such email already exists';
}
