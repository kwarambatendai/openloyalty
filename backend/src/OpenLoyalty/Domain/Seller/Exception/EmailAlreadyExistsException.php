<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Seller\Exception;

/**
 * Class EmailAlreadyExistsException.
 */
class EmailAlreadyExistsException extends SellerValidationException
{
    protected $message = 'seller with such email already exists';
}
