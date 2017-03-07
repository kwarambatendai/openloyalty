<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\CampaignBundle\Security\Voter;

use OpenLoyalty\Bundle\CampaignBundle\Service\CampaignProvider;
use OpenLoyalty\Bundle\UserBundle\Entity\User;
use OpenLoyalty\Domain\Campaign\Campaign;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class CampaignVoter.
 */
class CampaignVoter extends Voter
{
    const CREATE_CAMPAIGN = 'CREATE_CAMPAIGN';
    const EDIT = 'EDIT';
    const LIST_ALL_CAMPAIGNS = 'LIST_ALL_CAMPAIGNS';
    const LIST_ALL_VISIBLE_CAMPAIGNS = 'LIST_ALL_VISIBLE_CAMPAIGNS';
    const VIEW = 'VIEW';
    const LIST_CAMPAIGNS_AVAILABLE_FOR_ME = 'LIST_CAMPAIGNS_AVAILABLE_FOR_ME';
    const LIST_CAMPAIGNS_BOUGHT_BY_ME = 'LIST_CAMPAIGNS_BOUGHT_BY_ME';
    const BUY = 'BUY';
    const BUY_FOR_CUSTOMER = 'BUY_FOR_CUSTOMER';
    const MARK_COUPON_AS_USED = 'MARK_COUPON_AS_USED';

    /**
     * @var CampaignProvider
     */
    protected $campaignsProvider;

    /**
     * CampaignVoter constructor.
     *
     * @param CampaignProvider $campaignsProvider
     */
    public function __construct(CampaignProvider $campaignsProvider)
    {
        $this->campaignsProvider = $campaignsProvider;
    }

    public function supports($attribute, $subject)
    {
        return $subject instanceof Campaign && in_array($attribute, [
            self::EDIT, self::VIEW, self::BUY, self::MARK_COUPON_AS_USED,
        ]) || $subject == null && in_array($attribute, [
            self::CREATE_CAMPAIGN, self::LIST_ALL_CAMPAIGNS, self::LIST_ALL_VISIBLE_CAMPAIGNS, self::LIST_CAMPAIGNS_BOUGHT_BY_ME, self::LIST_CAMPAIGNS_AVAILABLE_FOR_ME,
            self::BUY_FOR_CUSTOMER,
        ]);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::CREATE_CAMPAIGN:
                return $user->hasRole('ROLE_ADMIN');
            case self::LIST_ALL_CAMPAIGNS:
                return $user->hasRole('ROLE_ADMIN');
            case self::LIST_ALL_VISIBLE_CAMPAIGNS:
                return $user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_SELLER');
            case self::BUY_FOR_CUSTOMER:
                return $user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_SELLER');
            case self::EDIT:
                return $user->hasRole('ROLE_ADMIN');
            case self::VIEW:
                return $this->canView($user, $subject);
            case self::LIST_CAMPAIGNS_AVAILABLE_FOR_ME:
                return $user->hasRole('ROLE_PARTICIPANT');
            case self::LIST_CAMPAIGNS_BOUGHT_BY_ME:
                return $user->hasRole('ROLE_PARTICIPANT');
            case self::BUY:
                return $user->hasRole('ROLE_PARTICIPANT');
            case self::MARK_COUPON_AS_USED:
                return $user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_PARTICIPANT');
            default:
                return false;
        }
    }

    protected function canView(User $user, $subject)
    {
        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }
        if ($user->hasRole('ROLE_SELLER')) {
            return true;
        }
        if ($user->hasRole('ROLE_PARTICIPANT')) {
            $customers = array_values($this->campaignsProvider->visibleForCustomers($subject));
            if (in_array($user->getId(), $customers)) {
                return true;
            }
        }

        return false;
    }
}
