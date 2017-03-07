<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\UserBundle\Security\Voter;

use OpenLoyalty\Bundle\UserBundle\Entity\User;
use OpenLoyalty\Domain\Seller\ReadModel\SellerDetails;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class SellerVoter.
 */
class SellerVoter extends Voter
{
    const LIST_SELLERS = 'LIST_SELLERS';
    const CREATE_SELLER = 'CREATE_SELLER';
    const VIEW = 'VIEW';
    const EDIT = 'EDIT';
    const DEACTIVATE = 'DEACTIVATE';
    const ACTIVATE = 'ACTIVATE';
    const DELETE = 'DELETE';

    public function supports($attribute, $subject)
    {
        return $subject instanceof SellerDetails && in_array($attribute, [
            self::VIEW, self::EDIT, self::ACTIVATE, self::DEACTIVATE, self::DELETE,
        ]) || $subject == null && in_array($attribute, [
            self::LIST_SELLERS, self::CREATE_SELLER,
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
            case self::LIST_SELLERS:
                return $user->hasRole('ROLE_ADMIN');
            case self::CREATE_SELLER:
                return $user->hasRole('ROLE_ADMIN');
            case self::DEACTIVATE:
                return $user->hasRole('ROLE_ADMIN');
            case self::ACTIVATE:
                return $user->hasRole('ROLE_ADMIN');
            case self::DELETE:
                return $user->hasRole('ROLE_ADMIN');
            case self::VIEW:
                return $this->canView($user, $subject);
            case self::EDIT:
                return $this->canEdit($user, $subject);
            default:
                return false;
        }
    }

    protected function canView(User $user, SellerDetails $subject)
    {
        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        if ($user->hasRole('ROLE_SELLER') && $subject->getSellerId() && $subject->getSellerId()->__toString() == $user->getId()) {
            return true;
        }

        return false;
    }

    protected function canEdit(User $user, SellerDetails $subject)
    {
        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        return false;
    }
}
