<?php

namespace OpenLoyalty\Domain\Account\Exception;

/**
 * Class CannotBeExpired.
 */
class CannotBeExpiredException extends \Exception
{
    protected $message = 'This transfer cannot be expired';
}
