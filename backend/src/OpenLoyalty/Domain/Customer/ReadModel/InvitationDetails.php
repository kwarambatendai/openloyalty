<?php

namespace OpenLoyalty\Domain\Customer\ReadModel;

use Broadway\ReadModel\ReadModelInterface;
use Broadway\Serializer\SerializableInterface;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\Invitation;
use OpenLoyalty\Domain\Customer\InvitationId;

/**
 * Class InvitationDetails.
 */
class InvitationDetails implements ReadModelInterface, SerializableInterface
{
    /**
     * @var InvitationId
     */
    private $invitationId;

    /**
     * @var CustomerId
     */
    private $referrerId;

    /**
     * @var string
     */
    private $referrerEmail;

    /**
     * @var string
     */
    private $referrerName;

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
    private $recipientName;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $token;

    /**
     * InvitationDetails constructor.
     *
     * @param InvitationId $invitationId
     * @param CustomerId   $referrerId
     * @param string       $referrerEmail
     * @param string       $referrerName
     * @param string       $recipientEmail
     * @param $token
     */
    public function __construct(
        InvitationId $invitationId,
        CustomerId $referrerId,
        $referrerEmail,
        $referrerName,
        $recipientEmail,
        $token
    ) {
        $this->invitationId = $invitationId;
        $this->referrerId = $referrerId;
        $this->referrerEmail = $referrerEmail;
        $this->referrerName = $referrerName;
        $this->recipientEmail = $recipientEmail;
        $this->status = Invitation::STATUS_INVITED;
        $this->token = $token;
    }

    public function updateRecipientData(CustomerId $recipientId = null, $recipientName = null)
    {
        if ($recipientId) {
            if ($this->recipientId) {
                throw new \InvalidArgumentException('Already assigned to user');
            }
            $this->status = Invitation::STATUS_REGISTERED;
            $this->recipientId = $recipientId;
        }
        if ($recipientName) {
            $this->recipientName = $recipientName;
        }
    }

    /**
     * @return InvitationId
     */
    public function getInvitationId()
    {
        return $this->invitationId;
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
    public function getReferrerEmail()
    {
        return $this->referrerEmail;
    }

    /**
     * @return string
     */
    public function getReferrerName()
    {
        return $this->referrerName;
    }

    /**
     * @return string
     */
    public function getRecipientEmail()
    {
        return $this->recipientEmail;
    }

    /**
     * @return CustomerId
     */
    public function getRecipientId()
    {
        return $this->recipientId;
    }

    /**
     * @return string
     */
    public function getRecipientName()
    {
        return $this->recipientName;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->invitationId->__toString();
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        $invitation = new self(
            new InvitationId($data['invitationId']),
            new CustomerId($data['referrerId']),
            $data['referrerEmail'],
            $data['referrerName'],
            $data['recipientEmail'],
            $data['token']
        );
        $invitation->updateRecipientData(
            $data['recipientId'] ? new CustomerId($data['recipientId']) : null,
            $data['recipientName']
        );

        $invitation->status = $data['status'];

        return $invitation;
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'invitationId' => $this->invitationId->__toString(),
            'referrerId' => $this->referrerId->__toString(),
            'referrerEmail' => $this->referrerEmail,
            'referrerName' => $this->referrerName,
            'recipientId' => $this->recipientId ? $this->recipientId->__toString() : null,
            'recipientEmail' => $this->recipientEmail,
            'recipientName' => $this->recipientName,
            'status' => $this->status,
            'token' => $this->token,
        ];
    }

    public function referrerIdAsString()
    {
        return (string) $this->referrerId;
    }

    public function recipientIdAsString()
    {
        return (string) $this->recipientId;
    }

    public function madePurchase()
    {
        $this->status = Invitation::STATUS_MADE_PURCHASE;
    }
}
