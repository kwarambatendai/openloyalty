<?php

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
