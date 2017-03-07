<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\EmailBundle\Service;

use OpenLoyalty\Bundle\EmailBundle\Model\Message;

/**
 * Class MessageFactory.
 */
class MessageFactory implements MessageFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return new Message();
    }
}
