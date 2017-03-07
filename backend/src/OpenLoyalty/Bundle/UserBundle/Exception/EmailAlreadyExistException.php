<?php

namespace OpenLoyalty\Bundle\UserBundle\Exception;

/**
 * Class EmailAlreadyExistException.
 */
class EmailAlreadyExistException extends \DomainException
{
    protected $message = 'This value is already used.';
}
