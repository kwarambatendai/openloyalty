<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\PosBundle\Security\Voter;

use OpenLoyalty\Bundle\UserBundle\Entity\User;
use OpenLoyalty\Domain\Pos\Pos;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class PosVoter.
 */
class PosVoter extends Voter
{
    const LIST_POS = 'LIST_POS';
    const EDIT = 'EDIT';
    const CREATE_POS = 'CREATE_POS';
    const VIEW = 'VIEW';

    public function supports($attribute, $subject)
    {
        return $subject instanceof Pos && in_array($attribute, [
            self::EDIT, self::VIEW,
        ]) || $subject == null && in_array($attribute, [
            self::LIST_POS, self::CREATE_POS,
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
            case self::LIST_POS:
                return $user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_SELLER');
            case self::EDIT:
                return $user->hasRole('ROLE_ADMIN');
            case self::CREATE_POS:
                return $user->hasRole('ROLE_ADMIN');
            case self::VIEW:
                return $user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_SELLER');
            default:
                return false;
        }
    }
}
