<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\UserBundle\Security\Voter;

use OpenLoyalty\Bundle\UserBundle\Entity\Admin;
use OpenLoyalty\Bundle\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class AdminVoter.
 */
class AdminVoter extends Voter
{
    const EDIT = 'EDIT';
    const VIEW = 'VIEW';
    const CREATE_USER = 'CREATE_USER';

    public function supports($attribute, $subject)
    {
        return $subject instanceof Admin && in_array($attribute, [
            self::VIEW, self::EDIT,
        ]) || in_array($attribute, [self::CREATE_USER]);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($user, $subject);
            case self::EDIT:
                return $this->canEdit($user, $subject);
            case self::CREATE_USER:
                return $this->canCreate($user);
            default:
                return false;
        }
    }

    protected function canView(User $user, User $subject)
    {
        if ($user->hasRole('ROLE_ADMIN') && $user instanceof Admin) {
            return true;
        }

        return false;
    }

    protected function canEdit(User $user, User $subject)
    {
        if ($user->hasRole('ROLE_ADMIN') && $user instanceof Admin) {
            return true;
        }

        return false;
    }

    protected function canCreate(User $user)
    {
        if ($user->hasRole('ROLE_ADMIN') && $user instanceof Admin) {
            return true;
        }

        return false;
    }
}
