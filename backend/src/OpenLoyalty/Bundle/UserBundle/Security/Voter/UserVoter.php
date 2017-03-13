<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UserBundle\Security\Voter;

use OpenLoyalty\Bundle\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class UserVoter.
 */
class UserVoter extends Voter
{
    const PASSWORD_CHANGE = 'PASSWORD_CHANGE';
    const REVOKE_REFRESH_TOKEN = 'REVOKE_REFRESH_TOKEN';

    public function supports($attribute, $subject)
    {
        return in_array($attribute, [
            self::PASSWORD_CHANGE,
            self::REVOKE_REFRESH_TOKEN,
        ]);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return true;
    }
}
