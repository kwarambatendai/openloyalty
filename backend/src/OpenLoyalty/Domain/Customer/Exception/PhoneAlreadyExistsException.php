<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Customer\Exception;

/**
 * Class PhoneAlreadyExistsException.
 */
class PhoneAlreadyExistsException extends CustomerValidationException
{
    protected $message = 'customer with such phone already exists';
}
