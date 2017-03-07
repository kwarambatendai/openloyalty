<?php

namespace OpenLoyalty\Domain\Account\Exception;

/**
 * Class NotEnoughPointsException.
 */
class NotEnoughPointsException extends \InvalidArgumentException
{
    protected $message = 'Not enough points';
}
