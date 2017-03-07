<?php

namespace OpenLoyalty\Domain\Account\Exception;

/**
 * Class CannotBeCanceled.
 */
class CannotBeCanceledException extends \Exception
{
    protected $message = 'this transfer cannot be canceled';
}
