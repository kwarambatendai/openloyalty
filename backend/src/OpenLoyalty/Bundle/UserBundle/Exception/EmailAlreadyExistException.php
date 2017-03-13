<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UserBundle\Exception;

/**
 * Class EmailAlreadyExistException.
 */
class EmailAlreadyExistException extends \DomainException
{
    protected $message = 'This value is already used.';
}
