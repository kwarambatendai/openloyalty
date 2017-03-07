<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\UserBundle\Exception;

/**
 * Class SellerIsNotActiveException.
 */
class SellerIsNotActiveException extends \Exception
{
    protected $message = 'Your account is not active';
}
