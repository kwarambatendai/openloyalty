<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Customer\Event;

use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\Invitation;
use OpenLoyalty\Domain\Customer\InvitationId;

/**
 * Class InvitationWasCreated.
 */
class InvitationWasCreated extends InvitationEvent
{
    /**
     * @var CustomerId
     */
    private $referrerId;

    /**
     * @var string
     */
    private $recipientEmail;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $token;

    public function __construct(InvitationId $invitationId, CustomerId $referrerId, $recipientEmail, $token)
    {
        parent::__construct($invitationId);
        $this->recipientEmail = $recipientEmail;
        $this->referrerId = $referrerId;
        $this->status = Invitation::STATUS_INVITED;
        $this->token = $token;
    }

    /**
     * @return CustomerId
     */
    public function getReferrerId()
    {
        return $this->referrerId;
    }

    /**
     * @return string
     */
    public function getRecipientEmail()
    {
        return $this->recipientEmail;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    public function serialize()
    {
        return array_merge(
            parent::serialize(),
            [
                'recipientEmail' => $this->recipientEmail,
                'referrerId' => (string) $this->referrerId,
                'status' => $this->status,
                'token' => $this->token,
            ]
        );
    }

    /**
     * @param array $data
     *
     * @return InvitationWasCreated
     */
    public static function deserialize(array $data)
    {
        $invitation = new self(
            new InvitationId($data['invitationId']),
            new CustomerId($data['referrerId']),
            $data['recipientEmail'],
            $data['token']
        );
        $invitation->status = $data['status'];

        return $invitation;
    }
}
