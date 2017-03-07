<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\AnalyticsBundle\Security\Voter;

use OpenLoyalty\Bundle\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class AnalyticsVoter.
 */
class AnalyticsVoter extends Voter
{
    const VIEW_STATS = 'VIEW_STATS';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject == null && in_array($attribute, [self::VIEW_STATS]);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW_STATS:
                return $user->hasRole('ROLE_ADMIN');
            default:
                return false;
        }
    }
}
