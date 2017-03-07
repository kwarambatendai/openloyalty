<?php

namespace OpenLoyalty\Domain\Seller\Exception;

/**
 * Class EmailAlreadyExistsException.
 */
class EmailAlreadyExistsException extends SellerValidationException
{
    protected $message = 'seller with such email already exists';
}
