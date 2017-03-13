<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UserBundle\Service;

use Doctrine\ORM\QueryBuilder;
use OpenLoyalty\Bundle\UserBundle\Entity\Seller;
use OpenLoyalty\Bundle\UserBundle\Entity\User;
use OpenLoyalty\Bundle\UserBundle\Exception\SellerIsNotActiveException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class SellerProvider.
 */
class SellerProvider extends UserProvider implements UserProviderInterface
{
    public function loadUserByUsername($username)
    {
        $user = $this->loadUserByUsernameOrEmail($username, Seller::class);

        if (!$user->getIsActive()) {
            throw new SellerIsNotActiveException();
        }

        if ($user instanceof Seller) {
            return $user;
        }

        throw new UsernameNotFoundException();
    }

    public function supportsClass($class)
    {
        return $class === 'Heal\SecurityBundle\Entity\Seller';
    }

    /**
     * @param $username
     * @param $class
     *
     * @return User
     */
    public function loadUserByUsernameOrEmail($username, $class)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->em->createQueryBuilder();
        $qb->select('u')->from($class, 'u');
        $qb->andWhere('u.username = :username or u.email = :username')->setParameter(':username', $username);
        $qb->andWhere('u.deletedAt is NULL');
        $user = $qb->getQuery()->getOneOrNullResult();

        if (!$user instanceof User) {
            throw new UsernameNotFoundException();
        }

        return $user;
    }
}
