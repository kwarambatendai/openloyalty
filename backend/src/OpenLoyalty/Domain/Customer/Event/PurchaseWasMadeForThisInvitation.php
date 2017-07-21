<?php

namespace OpenLoyalty\Domain\Customer\Event;

use OpenLoyalty\Domain\Customer\InvitationId;

/**
 * Class PurchaseWasMadeForThisInvitation.
 */
class PurchaseWasMadeForThisInvitation extends InvitationEvent
{
    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(new InvitationId($data['invitationId']));
    }
}
