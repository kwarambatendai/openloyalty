<?php

namespace OpenLoyalty\Bundle\UserBundle\Security\Voter;

use OpenLoyalty\Bundle\UserBundle\Entity\Customer;
use OpenLoyalty\Bundle\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class InvitationVoter.
 */
class InvitationVoter extends Voter
{
    const LIST_INVITATIONS = 'LIST_INVITATIONS';
    const INVITE = 'INVITE';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [
            self::INVITE,
            self::LIST_INVITATIONS,
        ]);
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
            case self::LIST_INVITATIONS:
                return $user->hasRole('ROLE_ADMIN');
            case self::INVITE:
                return $user instanceof Customer;
        }
    }
}
