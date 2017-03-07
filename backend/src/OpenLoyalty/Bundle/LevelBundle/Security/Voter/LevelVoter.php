<?php

namespace OpenLoyalty\Bundle\LevelBundle\Security\Voter;

use OpenLoyalty\Bundle\UserBundle\Entity\User;
use OpenLoyalty\Domain\Level\Level;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class LevelVoter.
 */
class LevelVoter extends Voter
{
    const CREATE_LEVEL = 'CREATE_LEVEL';
    const EDIT = 'EDIT';
    const LIST_LEVELS = 'LIST_LEVELS';
    const LIST_CUSTOMERS = 'LIST_CUSTOMERS';
    const VIEW = 'VIEW';
    const ACTIVATE = 'ACTIVATE';

    public function supports($attribute, $subject)
    {
        return $subject instanceof Level && in_array($attribute, [
            self::EDIT, self::VIEW, self::ACTIVATE, self::LIST_CUSTOMERS,
        ]) || $subject == null && in_array($attribute, [
            self::CREATE_LEVEL, self::LIST_LEVELS,
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
            case self::CREATE_LEVEL:
                return $user->hasRole('ROLE_ADMIN');
            case self::LIST_LEVELS:
                return $user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_SELLER');
            case self::EDIT:
                return $user->hasRole('ROLE_ADMIN');
            case self::VIEW:
                return $user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_SELLER');
            case self::LIST_CUSTOMERS:
                return $user->hasRole('ROLE_ADMIN');
            case self::ACTIVATE:
                return $user->hasRole('ROLE_ADMIN');
            default:
                return false;
        }
    }
}
