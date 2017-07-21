<?php

namespace OpenLoyalty\Domain\Customer\Service;

use OpenLoyalty\Domain\Customer\CustomerId;

/**
 * Class ShaInvitationTokenGenerator.
 */
class ShaInvitationTokenGenerator implements InvitationTokenGenerator
{
    /**
     * @param CustomerId $referrerId
     * @param string     $recipientEmail
     *
     * @return string
     */
    public function generate(CustomerId $referrerId, $recipientEmail)
    {
        return sha1($referrerId->__toString().time().$recipientEmail.random_bytes(10));
    }
}
