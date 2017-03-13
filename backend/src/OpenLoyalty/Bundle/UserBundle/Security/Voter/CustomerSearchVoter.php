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
 * Class CustomerSearchVoter.
 */
class CustomerSearchVoter extends Voter
{
    const SEARCH_CUSTOMER = 'SEARCH_CUSTOMER';

    public function supports($attribute, $subject)
    {
        return $subject == null && in_array($attribute, [
            self::SEARCH_CUSTOMER,
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
            case self::SEARCH_CUSTOMER:
                return $user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_SELLER');
            default:
                return false;
        }
    }
}
