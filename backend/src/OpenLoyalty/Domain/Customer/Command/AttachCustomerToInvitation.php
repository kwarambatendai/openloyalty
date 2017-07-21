<?php

namespace OpenLoyalty\Domain\Customer\Command;

use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\InvitationId;

/**
 * Class AttachCustomerToInvitation.
 */
class AttachCustomerToInvitation extends InvitationCommand
{
    /**
     * @var CustomerId
     */
    private $customerId;

    public function __construct(InvitationId $invitationId, CustomerId $customerId)
    {
        parent::__construct($invitationId);
        $this->customerId = $customerId;
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }
}
