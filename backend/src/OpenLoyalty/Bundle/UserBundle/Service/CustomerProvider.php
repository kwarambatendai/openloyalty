<?php

namespace OpenLoyalty\Bundle\UserBundle\Service;

use Broadway\ReadModel\RepositoryInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use OpenLoyalty\Bundle\UserBundle\Entity\Customer;
use OpenLoyalty\Bundle\UserBundle\Entity\User;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class CustomerProvider.
 */
class CustomerProvider extends UserProvider implements UserProviderInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $customerDetailsRepository;

    /**
     * CustomerProvider constructor.
     *
     * @param EntityManager       $em
     * @param RepositoryInterface $customerDetailsRepository
     */
    public function __construct(EntityManager $em, RepositoryInterface $customerDetailsRepository)
    {
        parent::__construct($em);
        $this->customerDetailsRepository = $customerDetailsRepository;
    }

    public function loadUserByUsername($username)
    {
        try {
            $user = $this->loadUserByUsernameOrEmail($username, Customer::class);
            if ($user instanceof Customer) {
                return $user;
            }
        } catch (UsernameNotFoundException $e) {
        }

        try {
            $user = $this->loadUserByLoyaltyCardNumber($username);
            if ($user instanceof Customer) {
                return $user;
            }
        } catch (UsernameNotFoundException $e) {
        }

        $user = $this->loadUserByPhoneNumber($username);
        if ($user instanceof Customer) {
            return $user;
        }
    }

    public function loadUserByLoyaltyCardNumber($number)
    {
        $customers = $this->customerDetailsRepository->findBy(['loyaltyCardNumber' => $number]);
        if (count($customers) > 0) {
            /** @var CustomerDetails $customer */
            $customer = reset($customers);

            return $this->findUserByCustomerId($customer->getCustomerId());
        }

        throw new UsernameNotFoundException();
    }

    public function loadUserByPhoneNumber($number)
    {
        $customers = $this->customerDetailsRepository->findBy(['phone' => $number]);
        if (count($customers) > 0) {
            /** @var CustomerDetails $customer */
            $customer = reset($customers);

            return $this->findUserByCustomerId($customer->getCustomerId());
        }

        throw new UsernameNotFoundException();
    }

    public function supportsClass($class)
    {
        return $class === 'Heal\SecurityBundle\Entity\Customer';
    }

    protected function findUserByCustomerId(CustomerId $customerId)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->em->createQueryBuilder();
        $qb->select('u')->from('OpenLoyaltyUserBundle:User', 'u');
        $qb->andWhere('u.id = :id')->setParameter(':id', $customerId->__toString());
        $qb->andWhere('u.isActive = :true')->setParameter(':true', true);
        $user = $qb->getQuery()->getOneOrNullResult();

        if (!$user instanceof User) {
            throw new UsernameNotFoundException();
        }

        return $user;
    }
}
