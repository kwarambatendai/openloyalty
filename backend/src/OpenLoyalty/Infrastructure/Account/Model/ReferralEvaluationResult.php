<?php

namespace OpenLoyalty\Infrastructure\Account\Model;

use OpenLoyalty\Domain\Customer\ReadModel\InvitationDetails;

/**
 * Class ReferralEvaluationResult.
 */
class ReferralEvaluationResult extends EvaluationResult
{
    /**
     * @var string
     */
    protected $rewardType;

    /**
     * @var InvitationDetails
     */
    protected $invitation;

    public function __construct($earningRuleId, $points, $rewardType, InvitationDetails $invitationDetails)
    {
        parent::__construct($earningRuleId, $points);
        $this->rewardType = $rewardType;
        $this->invitation = $invitationDetails;
    }

    /**
     * @return string
     */
    public function getRewardType()
    {
        return $this->rewardType;
    }

    /**
     * @return InvitationDetails
     */
    public function getInvitation()
    {
        return $this->invitation;
    }
}
