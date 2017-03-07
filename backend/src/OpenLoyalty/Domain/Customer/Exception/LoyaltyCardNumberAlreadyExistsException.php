<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Customer\Exception;

/**
 * Class LoyaltyCardNumberAlreadyExistsException.
 */
class LoyaltyCardNumberAlreadyExistsException extends CustomerValidationException
{
    protected $message = 'customer with such loyalty card number already exists';
}
