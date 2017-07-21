<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Customer\Event;

use Broadway\Serializer\SerializableInterface;
use OpenLoyalty\Domain\Customer\InvitationId;

/**
 * Class InvitationEvent.
 */
abstract class InvitationEvent implements SerializableInterface
{
    /**
     * @var InvitationId
     */
    private $invitationId;

    /**
     * InvitationEvent constructor.
     *
     * @param InvitationId $invitationId
     */
    public function __construct(InvitationId $invitationId)
    {
        $this->invitationId = $invitationId;
    }

    /**
     * @return InvitationId
     */
    public function getInvitationId()
    {
        return $this->invitationId;
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return ['invitationId' => (string) $this->invitationId];
    }
}
