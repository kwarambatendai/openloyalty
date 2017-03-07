<?php

namespace OpenLoyalty\Bundle\UserBundle\Exception;

/**
 * Class ProviderTypeNotImplementedException.
 */
class ProviderTypeNotImplementedException extends \RuntimeException
{
    public function __construct($providerType)
    {
        parent::__construct(sprintf('The %s() is not implemented.', $providerType));
    }
}
