<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

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
