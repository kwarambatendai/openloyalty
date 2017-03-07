<?php

namespace OpenLoyalty\Bundle\UserBundle\Security\Voter;

use OpenLoyalty\Bundle\UserBundle\Entity\User;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Seller\ReadModel\SellerDetailsRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class CustomerVoter.
 */
class CustomerVoter extends Voter
{
    const LIST_CUSTOMERS = 'LIST_CUSTOMERS';
    const CREATE_CUSTOMER = 'CREATE_CUSTOMER';
    const VIEW = 'VIEW';
    const EDIT = 'EDIT';
    const VIEW_STATUS = 'VIEW_STATUS';
    const ADD_TO_LEVEL = 'ADD_TO_LEVEL';
    const ASSIGN_POS = 'ASSIGN_POS';
    const DEACTIVATE = 'DEACTIVATE';
    const ACTIVATE = 'ACTIVATE';

    /**
     * @var SellerDetailsRepository
     */
    protected $sellerDetailsRepository;

    /**
     * CustomerVoter constructor.
     *
     * @param SellerDetailsRepository $sellerDetailsRepository
     */
    public function __construct(SellerDetailsRepository $sellerDetailsRepository)
    {
        $this->sellerDetailsRepository = $sellerDetailsRepository;
    }

    public function supports($attribute, $subject)
    {
        return $subject instanceof CustomerDetails && in_array($attribute, [
            self::VIEW, self::EDIT, self::VIEW_STATUS, self::ADD_TO_LEVEL, self::ASSIGN_POS, self::DEACTIVATE, self::ACTIVATE,
        ]) || $subject == null && in_array($attribute, [
            self::LIST_CUSTOMERS, self::CREATE_CUSTOMER,
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
            case self::LIST_CUSTOMERS:
                return $user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_SELLER');
            case self::CREATE_CUSTOMER:
                return $this->canCreate($user);
            case self::ASSIGN_POS:
                return $this->canAssignPos($user, $subject);
            case self::DEACTIVATE:
                return $user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_SELLER');
            case self::ACTIVATE:
                return $user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_SELLER');
            case self::ADD_TO_LEVEL:
                return $this->canAddToLevel($user, $subject);
            case self::VIEW:
                return $this->canView($user, $subject);
            case self::VIEW_STATUS:
                return $this->canView($user, $subject);
            case self::EDIT:
                return $this->canEdit($user, $subject);
            default:
                return false;
        }
    }

    protected function canView(User $user, CustomerDetails $subject)
    {
        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        if ($user->hasRole('ROLE_PARTICIPANT') && $subject->getCustomerId() && $subject->getCustomerId()->__toString() == $user->getId()) {
            return true;
        }

        if ($user->hasRole('ROLE_SELLER')) {
            return true;
        }

        return false;
    }

    protected function canAssignPos(User $user, CustomerDetails $subject)
    {
        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        if ($user->hasRole('ROLE_SELLER')) {
            return true;
        }

        return false;
    }

    protected function canAddToLevel(User $user, CustomerDetails $subject)
    {
        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        if ($user->hasRole('ROLE_SELLER')) {
            return true;
        }

        return false;
    }

    protected function canEdit(User $user, CustomerDetails $subject)
    {
        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        if ($user->hasRole('ROLE_PARTICIPANT') && $subject->getCustomerId() && $subject->getCustomerId()->__toString() == $user->getId()) {
            return true;
        }

        if ($user->hasRole('ROLE_SELLER')) {
            return true;
        }

        return false;
    }

    protected function canCreate(User $user)
    {
        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        if ($user->hasRole('ROLE_SELLER')) {
            return true;
        }

        return false;
    }
}
