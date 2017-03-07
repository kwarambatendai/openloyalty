<?php

namespace OpenLoyalty\Bundle\UtilityBundle\Security\Voter;

use OpenLoyalty\Bundle\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class UtilityVoter.
 */
class UtilityVoter extends Voter
{
    const GENERATE_SEGMENT_CSV = 'GENERATE_SEGMENT_CSV';

    /**
     * @param string $attribute
     * @param mixed  $subject
     *
     * @return bool
     */
    public function supports($attribute, $subject)
    {
        return $subject == null && in_array($attribute, [self::GENERATE_SEGMENT_CSV]);
    }

    /**
     * @param string         $attribute
     * @param mixed          $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::GENERATE_SEGMENT_CSV:
                return $user->hasRole('ROLE_ADMIN');
            default:
                return false;
        }
    }
}
