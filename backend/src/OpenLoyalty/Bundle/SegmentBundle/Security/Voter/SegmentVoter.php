<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\SegmentBundle\Security\Voter;

use OpenLoyalty\Bundle\UserBundle\Entity\User;
use OpenLoyalty\Domain\Segment\Segment;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class SegmentVoter.
 */
class SegmentVoter extends Voter
{
    const LIST_SEGMENTS = 'LIST_SEGMENTS';
    const LIST_CUSTOMERS = 'LIST_CUSTOMERS';
    const EDIT = 'EDIT';
    const ACTIVATE = 'ACTIVATE';
    const DEACTIVATE = 'DEACTIVATE';
    const DELETE = 'DELETE';
    const CREATE_SEGMENT = 'CREATE_SEGMENT';
    const VIEW = 'VIEW';

    public function supports($attribute, $subject)
    {
        return $subject instanceof Segment && in_array($attribute, [
            self::EDIT, self::VIEW, self::DEACTIVATE, self::ACTIVATE, self::LIST_CUSTOMERS, self::DELETE,
        ]) || $subject == null && in_array($attribute, [
            self::LIST_SEGMENTS, self::CREATE_SEGMENT,
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
            case self::LIST_SEGMENTS:
                return $user->hasRole('ROLE_ADMIN');
            case self::EDIT:
                return $user->hasRole('ROLE_ADMIN');
            case self::CREATE_SEGMENT:
                return $user->hasRole('ROLE_ADMIN');
            case self::VIEW:
                return $user->hasRole('ROLE_ADMIN');
            case self::ACTIVATE:
                return $user->hasRole('ROLE_ADMIN');
            case self::DEACTIVATE:
                return $user->hasRole('ROLE_ADMIN');
            case self::DELETE:
                return $user->hasRole('ROLE_ADMIN');
            case self::LIST_CUSTOMERS:
                return $user->hasRole('ROLE_ADMIN');
            default:
                return false;
        }
    }
}
