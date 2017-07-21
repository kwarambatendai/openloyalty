<?php

namespace OpenLoyalty\Bundle\UserBundle\Event;

use OpenLoyalty\Domain\Customer\CustomerId;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class UserRegisteredWithInvitationToken.
 */
class UserRegisteredWithInvitationToken extends Event
{
    const NAME = 'user.invitation.user_registered_with_invitation_token';

    private $invitationToken;

    /**
     * @var CustomerId
     */
    private $customerId;

    /**
     * UserRegisteredWithInvitationToken constructor.
     *
     * @param $invitationToken
     * @param CustomerId $customerId
     */
    public function __construct($invitationToken, CustomerId $customerId)
    {
        $this->invitationToken = $invitationToken;
        $this->customerId = $customerId;
    }

    /**
     * @return mixed
     */
    public function getInvitationToken()
    {
        return $this->invitationToken;
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }
}
