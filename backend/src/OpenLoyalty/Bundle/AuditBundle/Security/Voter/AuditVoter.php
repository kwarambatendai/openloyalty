<?php

namespace OpenLoyalty\Bundle\AuditBundle\Security\Voter;

use OpenLoyalty\Bundle\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class AuditVoter.
 */
class AuditVoter extends Voter
{
    const AUDIT_LOG = 'AUDIT_LOG';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject == null && in_array($attribute, [
                self::AUDIT_LOG,
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
            case self::AUDIT_LOG:
                return $user->hasRole('ROLE_ADMIN');
            default:
                return false;
        }
    }
}
