<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\EmailBundle\Mailer;

use OpenLoyalty\Bundle\EmailBundle\Model\MessageInterface;

/**
 * Interface OloyMailer.
 */
interface OloyMailer
{
    /**
     * @param MessageInterface $message
     *
     * @return bool
     */
    public function send(MessageInterface $message);
}
