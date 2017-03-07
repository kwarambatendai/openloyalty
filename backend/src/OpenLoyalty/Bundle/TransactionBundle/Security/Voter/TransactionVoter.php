<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\TransactionBundle\Security\Voter;

use OpenLoyalty\Bundle\UserBundle\Entity\User;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Seller\ReadModel\SellerDetailsRepository;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetails;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class TransactionVoter.
 */
class TransactionVoter extends Voter
{
    const LIST_TRANSACTIONS = 'LIST_TRANSACTIONS';
    const LIST_CURRENT_CUSTOMER_TRANSACTIONS = 'LIST_CURRENT_CUSTOMER_TRANSACTIONS';
    const LIST_CURRENT_POS_TRANSACTIONS = 'LIST_CURRENT_POS_TRANSACTIONS';
    const LIST_ITEM_LABELS = 'LIST_ITEM_LABELS';
    const VIEW = 'VIEW';
    const CREATE_TRANSACTION = 'CREATE_TRANSACTION';
    const ASSIGN_CUSTOMER_TO_TRANSACTION = 'ASSIGN_CUSTOMER_TO_TRANSACTION';
    const LIST_CUSTOMER_TRANSACTIONS = 'LIST_CUSTOMER_TRANSACTIONS';

    /**
     * @var SellerDetailsRepository
     */
    protected $sellerDetailsRepository;

    /**
     * TransactionVoter constructor.
     *
     * @param SellerDetailsRepository $sellerDetailsRepository
     */
    public function __construct(SellerDetailsRepository $sellerDetailsRepository)
    {
        $this->sellerDetailsRepository = $sellerDetailsRepository;
    }

    public function supports($attribute, $subject)
    {
        return $subject instanceof TransactionDetails && in_array($attribute, [
            self::VIEW, self::ASSIGN_CUSTOMER_TO_TRANSACTION,
        ]) || $subject == null && in_array($attribute, [
            self::LIST_TRANSACTIONS, self::LIST_CURRENT_CUSTOMER_TRANSACTIONS, self::LIST_CURRENT_POS_TRANSACTIONS,
            self::LIST_ITEM_LABELS, self::CREATE_TRANSACTION,
        ]) || $subject instanceof CustomerDetails && in_array($attribute, [
            self::LIST_CUSTOMER_TRANSACTIONS,
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
            case self::LIST_TRANSACTIONS:
                return $user->hasRole('ROLE_ADMIN');
            case self::CREATE_TRANSACTION:
                return $user->hasRole('ROLE_ADMIN');
            case self::ASSIGN_CUSTOMER_TO_TRANSACTION:
                return $this->canAssign($user, $subject);
            case self::LIST_CURRENT_POS_TRANSACTIONS:
                return $user->hasRole('ROLE_SELLER');
            case self::LIST_CURRENT_CUSTOMER_TRANSACTIONS:
                return $user->hasRole('ROLE_PARTICIPANT');
            case self::LIST_CUSTOMER_TRANSACTIONS:
                return $user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_PARTICIPANT');
            case self::VIEW:
                return $this->canView($user, $subject);
            case self::LIST_ITEM_LABELS:
                return true;
            default:
                return false;
        }
    }

    protected function canView(User $user, TransactionDetails $subject)
    {
        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        if ($user->hasRole('ROLE_SELLER')) {
            return true;
        }

        if ($user->hasRole('ROLE_PARTICIPANT') && $subject->getCustomerId() && $subject->getCustomerId()->__toString() == $user->getId()) {
            return true;
        }

        return false;
    }

    protected function canAssign(User $user, TransactionDetails $subject)
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
