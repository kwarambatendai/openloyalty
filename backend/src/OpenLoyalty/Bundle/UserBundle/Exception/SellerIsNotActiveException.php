<?php

namespace OpenLoyalty\Bundle\UserBundle\Exception;

/**
 * Class SellerIsNotActiveException.
 */
class SellerIsNotActiveException extends \Exception
{
    protected $message = 'Your account is not active';
}
