<?php

namespace OpenLoyalty\Bundle\PointsBundle\Security\Voter;

use OpenLoyalty\Bundle\UserBundle\Entity\User;
use OpenLoyalty\Domain\Account\ReadModel\PointsTransferDetails;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class PointsTransferVoter.
 */
class PointsTransferVoter extends Voter
{
    const LIST_POINTS_TRANSFERS = 'LIST_POINTS_TRANSFERS';
    const ADD_POINTS = 'ADD_POINTS';
    const SPEND_POINTS = 'SPEND_POINTS';
    const CANCEL = 'CANCEL';
    const LIST_CUSTOMER_POINTS_TRANSFERS = 'LIST_CUSTOMER_POINTS_TRANSFERS';

    public function supports($attribute, $subject)
    {
        return $subject instanceof PointsTransferDetails && in_array($attribute, [
            self::CANCEL,
        ]) || $subject == null && in_array($attribute, [
            self::LIST_CUSTOMER_POINTS_TRANSFERS, self::LIST_POINTS_TRANSFERS, self::ADD_POINTS, self::SPEND_POINTS,
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
            case self::LIST_POINTS_TRANSFERS:
                return $user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_SELLER');
            case self::ADD_POINTS:
                return $user->hasRole('ROLE_ADMIN');
            case self::SPEND_POINTS:
                return $user->hasRole('ROLE_ADMIN');
            case self::CANCEL:
                return $user->hasRole('ROLE_ADMIN');
            case self::LIST_CUSTOMER_POINTS_TRANSFERS:
                return $user->hasRole('ROLE_PARTICIPANT');
            default:
                return false;
        }
    }
}
