<?php

namespace OpenLoyalty\Bundle\UserBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use OpenLoyalty\Bundle\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * User constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function loadUserByUsername($username)
    {
        return $this->loadUserByUsernameOrEmail($username, User::class);
    }

    public function loadUserByUsernameOrEmail($username, $class)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->em->createQueryBuilder();
        $qb->select('u')->from($class, 'u');
        $qb->andWhere('u.username = :username or u.email = :username')->setParameter(':username', $username);
        $qb->andWhere('u.isActive = :true')->setParameter('true', true);
        $qb->andWhere('u.deletedAt is NULL');
        $user = $qb->getQuery()->getOneOrNullResult();

        if (!$user instanceof User) {
            throw new UsernameNotFoundException();
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return User::class == $class;
    }
}
