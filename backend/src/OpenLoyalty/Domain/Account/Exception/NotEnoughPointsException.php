<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Account\Exception;

/**
 * Class NotEnoughPointsException.
 */
class NotEnoughPointsException extends \InvalidArgumentException
{
    protected $message = 'Not enough points';
}
