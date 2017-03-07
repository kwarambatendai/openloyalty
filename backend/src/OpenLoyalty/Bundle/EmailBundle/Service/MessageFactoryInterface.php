<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\EmailBundle\Service;

use OpenLoyalty\Bundle\EmailBundle\Model\MessageInterface;

/**
 * Interface MessageFactoryInterface.
 */
interface MessageFactoryInterface
{
    /**
     * Create message object.
     *
     * @return MessageInterface
     */
    public function create();
}
