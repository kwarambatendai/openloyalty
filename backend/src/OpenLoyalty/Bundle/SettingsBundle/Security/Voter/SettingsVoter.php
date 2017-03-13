<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\SettingsBundle\Security\Voter;

use OpenLoyalty\Bundle\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class SettingsVoter.
 */
class SettingsVoter extends Voter
{
    const VIEW_SETTINGS_CHOICES = 'VIEW_SETTINGS_CHOICES';
    const VIEW_SETTINGS = 'VIEW_SETTINGS';
    const EDIT_SETTINGS = 'EDIT_SETTINGS';

    public function supports($attribute, $subject)
    {
        return $subject == null && in_array($attribute, [
            self::VIEW_SETTINGS, self::VIEW_SETTINGS_CHOICES, self::EDIT_SETTINGS,
        ]);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($attribute == self::VIEW_SETTINGS_CHOICES) {
            return true;
        }

        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW_SETTINGS:
                return $user->hasRole('ROLE_ADMIN');
            case self::EDIT_SETTINGS:
                return $user->hasRole('ROLE_ADMIN');
            default:
                return false;
        }
    }
}
