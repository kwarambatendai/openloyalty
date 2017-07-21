<?php

namespace OpenLoyalty\Domain\Customer\SystemEvent;

use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\InvitationId;

/**
 * Class CustomerAttachedToInvitationSystemEvent.
 */
class CustomerAttachedToInvitationSystemEvent
{
    /**
     * @var CustomerId
     */
    protected $customerId;

    /**
     * @var InvitationId
     */
    protected $invitationId;

    /**
     * CustomerSystemEvent constructor.
     *
     * @param CustomerId   $customerId
     * @param InvitationId $invitationId
     */
    public function __construct(CustomerId $customerId, InvitationId $invitationId)
    {
        $this->customerId = $customerId;
        $this->invitationId = $invitationId;
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return InvitationId
     */
    public function getInvitationId()
    {
        return $this->invitationId;
    }
}
