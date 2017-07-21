<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Customer;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use OpenLoyalty\Domain\Customer\Event\CustomerWasAttachedToInvitation;
use OpenLoyalty\Domain\Customer\Event\InvitationWasCreated;
use OpenLoyalty\Domain\Customer\Event\PurchaseWasMadeForThisInvitation;

/**
 * Class Invitation.
 */
class Invitation extends EventSourcedAggregateRoot
{
    const STATUS_INVITED = 'invited';
    const STATUS_REGISTERED = 'registered';
    const STATUS_MADE_PURCHASE = 'made_purchase';

    /**
     * @var InvitationId
     */
    private $id;

    /**
     * @var CustomerId
     */
    private $referrerId;

    /**
     * @var string
     */
    private $recipientEmail;

    /**
     * @var CustomerId
     */
    private $recipientId;

    /**
     * @var string
     */
    private $status = self::STATUS_INVITED;

    /**
     * @return string
     */
    public function getAggregateRootId()
    {
        return $this->id;
    }

    public static function createInvitation(InvitationId $invitationId, CustomerId $referrerId, $recipientEmail, $token)
    {
        $invitation = new self();
        $invitation->create($invitationId, $referrerId, $recipientEmail, $token);

        return $invitation;
    }

    public function attachCustomer(CustomerId $customerId)
    {
        $this->apply(
            new CustomerWasAttachedToInvitation($this->id, $customerId)
        );
    }

    public function purchaseMade()
    {
        $this->apply(
            new PurchaseWasMadeForThisInvitation($this->id)
        );
    }

    private function create(InvitationId $invitationId, CustomerId $referrerId, $recipientEmail, $token)
    {
        $this->apply(
            new InvitationWasCreated($invitationId, $referrerId, $recipientEmail, $token)
        );
    }

    public function applyInvitationWasCreated(InvitationWasCreated $event)
    {
        $this->setId($event->getInvitationId());
        $this->setRecipientEmail($event->getRecipientEmail());
        $this->setReferrerId($event->getReferrerId());
    }

    public function applyCustomerWasAttachedToInvitation(CustomerWasAttachedToInvitation $event)
    {
        $this->recipientId = $event->getCustomerId();
    }

    /**
     * @param InvitationId $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return CustomerId
     */
    public function getReferrerId()
    {
        return $this->referrerId;
    }

    /**
     * @param CustomerId $referrerId
     */
    public function setReferrerId($referrerId)
    {
        $this->referrerId = $referrerId;
    }

    /**
     * @return string
     */
    public function getRecipientEmail()
    {
        return $this->recipientEmail;
    }

    /**
     * @param string $recipientEmail
     */
    public function setRecipientEmail($recipientEmail)
    {
        $this->recipientEmail = $recipientEmail;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
}
