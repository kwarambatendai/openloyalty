<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\UserBundle\Service;

use Doctrine\ORM\QueryBuilder;
use OpenLoyalty\Bundle\UserBundle\Entity\Admin;
use OpenLoyalty\Bundle\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class AdminProvider.
 */
class AdminProvider extends UserProvider implements UserProviderInterface
{
    public function loadUserByUsername($username)
    {
        $user = $this->loadUserByUsernameOrEmail($username, Admin::class);
        if ($user instanceof Admin) {
            return $user;
        }

        throw new UsernameNotFoundException();
    }

    public function loadUserByApiKey($apiKey)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->em->createQueryBuilder();
        $qb->select('u')->from(Admin::class, 'u');
        $qb->andWhere('u.apiKey = :username')->setParameter(':username', $apiKey);
        $qb->andWhere('u.external = true');
        $qb->andWhere('u.isActive = :true')->setParameter('true', true);
        $qb->andWhere('u.deletedAt is NULL');
        $user = $qb->getQuery()->getOneOrNullResult();

        if (!$user instanceof User) {
            throw new UsernameNotFoundException();
        }

        return $user;
    }

    public function supportsClass($class)
    {
        return $class === 'Heal\SecurityBundle\Entity\Admin';
    }
}
