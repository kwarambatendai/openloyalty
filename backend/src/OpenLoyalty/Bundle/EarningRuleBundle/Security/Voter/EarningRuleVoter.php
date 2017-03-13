<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\EarningRuleBundle\Security\Voter;

use OpenLoyalty\Bundle\UserBundle\Entity\User;
use OpenLoyalty\Domain\EarningRule\EarningRule;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class EarningRuleVoter.
 */
class EarningRuleVoter extends Voter
{
    const CREATE_EARNING_RULE = 'CREATE_EARNING_RULE';
    const EDIT = 'EDIT';
    const LIST_ALL_EARNING_RULES = 'LIST_ALL_EARNING_RULES';
    const VIEW = 'VIEW';
    const LIST_ACTIVE_EARNING_RULES = 'LIST_ACTIVE_EARNING_RULES';
    const ACTIVATE = 'ACTIVATE';

    public function supports($attribute, $subject)
    {
        return $subject instanceof EarningRule && in_array($attribute, [
            self::EDIT, self::VIEW, self::ACTIVATE,
        ]) || $subject == null && in_array($attribute, [
            self::CREATE_EARNING_RULE, self::LIST_ALL_EARNING_RULES, self::LIST_ACTIVE_EARNING_RULES,
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
            case self::CREATE_EARNING_RULE:
                return $user->hasRole('ROLE_ADMIN');
            case self::LIST_ALL_EARNING_RULES:
                return $user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_SELLER');
            case self::EDIT:
                return $user->hasRole('ROLE_ADMIN');
            case self::ACTIVATE:
                return $user->hasRole('ROLE_ADMIN');
            case self::VIEW:
                return $user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_SELLER');
            case self::LIST_ACTIVE_EARNING_RULES:
                return $this->canListActive($user);
            default:
                return false;
        }
    }

    protected function canListActive(User $user)
    {
        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }
        if ($user->hasRole('ROLE_PARTICIPANT')) {
            return true;
        }

        return false;
    }
}
