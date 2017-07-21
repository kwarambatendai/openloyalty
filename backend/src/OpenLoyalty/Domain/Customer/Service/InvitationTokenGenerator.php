<?php

namespace OpenLoyalty\Domain\Customer\Service;

use OpenLoyalty\Domain\Customer\CustomerId;

interface InvitationTokenGenerator
{
    /**
     * @param CustomerId $referrerId
     * @param string     $recipientEmail
     *
     * @return string
     */
    public function generate(CustomerId $referrerId, $recipientEmail);
}
