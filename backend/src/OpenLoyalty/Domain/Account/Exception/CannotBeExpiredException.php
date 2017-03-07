<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Account\Exception;

/**
 * Class CannotBeExpired.
 */
class CannotBeExpiredException extends \Exception
{
    protected $message = 'This transfer cannot be expired';
}
