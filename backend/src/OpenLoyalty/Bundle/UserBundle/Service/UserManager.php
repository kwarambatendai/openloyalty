<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UserBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use OpenLoyalty\Bundle\UserBundle\Entity\Admin;
use OpenLoyalty\Bundle\UserBundle\Entity\Customer;
use OpenLoyalty\Bundle\UserBundle\Entity\Seller;
use OpenLoyalty\Bundle\UserBundle\Entity\User;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Repository\Customer\CustomerDetailsElasticsearchRepository;
use OpenLoyalty\Domain\Seller\SellerId;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserManager.
 */
class UserManager
{
    /**
     * @var UserPasswordEncoderInterface
     */
    protected $passwordEncoder;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var PasswordGenerator
     */
    protected $passwordGenerator;

    /**
     * @var EmailProvider
     */
    protected $emailProvider;

    /**
     * @var
     */
    protected $customerDetailsRepository;

    /**
     * UserManager constructor.
     *
     * @param UserPasswordEncoderInterface           $passwordEncoder
     * @param EntityManager                          $em
     * @param PasswordGenerator                      $passwordGenerator
     * @param EmailProvider                          $emailProvider
     * @param CustomerDetailsElasticsearchRepository $customerDetailsRepository
     */
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManager $em,
        PasswordGenerator $passwordGenerator,
        EmailProvider $emailProvider,
        CustomerDetailsElasticsearchRepository $customerDetailsRepository
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
        $this->passwordGenerator = $passwordGenerator;
        $this->emailProvider = $emailProvider;
        $this->customerDetailsRepository = $customerDetailsRepository;
    }

    public function updateUser(User $user, $andFlush = true)
    {
        $this->updatePassword($user);
        $this->em->persist($user);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    public function updatePassword(User $user)
    {
        if (0 !== strlen($password = $user->getPlainPassword())) {
            $encoder = $this->getEncoder($user);
            $user->setPassword($encoder->encodePassword($user, $password));
            $user->eraseCredentials();
        }
    }

    public function isCustomerExist($email)
    {
        return $this->em->getRepository('OpenLoyaltyUserBundle:Customer')
            ->findOneBy(['email' => $email]) ? true : false;
    }

    public function isSellerExist($email)
    {
        return $this->em->getRepository('OpenLoyaltyUserBundle:Seller')
            ->findOneBy(['email' => $email]) ? true : false;
    }

    public function createNewCustomer(CustomerId $customerId, $email, $password = null, $emailDisabled = false)
    {
        $user = new Customer($customerId);
        $user->setEmail($email);
        $sendEmail = false;

        if (!$password) {
            $user->setTemporaryPasswordSetAt(new \DateTime());
            $password = $this->passwordGenerator->generate();
            $sendEmail = true;
        }
        $user->setPlainPassword($password);
        $role = $this->em->getRepository('OpenLoyaltyUserBundle:Role')->findOneBy(['role' => 'ROLE_PARTICIPANT']);
        if ($role) {
            $user->addRole($role);
        }
        $this->updateUser($user);

        if ($sendEmail && !$emailDisabled) {
            $customerDetails = $this->customerDetailsRepository->find($user->getId());
            $this->emailProvider->registrationWithTemporaryPassword(
                $customerDetails,
                $user->getPlainPassword()
            );
        }

        return $user;
    }

    public function findUserByUsernameOrEmail($username, $class = User::class)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->em->createQueryBuilder();
        $qb->select('u')->from($class, 'u');
        $qb->andWhere('u.username = :username or u.email = :username')->setParameter(':username', $username);
        $qb->andWhere('u.isActive = :true')->setParameter('true', true);
        $qb->andWhere('u.deletedAt is NULL');
        $user = $qb->getQuery()->getOneOrNullResult();

        if (!$user instanceof User) {
            return;
        }

        return $user;
    }

    public function findUserByConfirmationToken($token)
    {
        return $this->em->getRepository('OpenLoyaltyUserBundle:User')->findOneBy(['confirmationToken' => $token]);
    }

    public function createNewSeller(SellerId $sellerId, $email, $password)
    {
        $user = new Seller($sellerId);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $role = $this->em->getRepository('OpenLoyaltyUserBundle:Role')->findOneBy(['role' => 'ROLE_SELLER']);
        if ($role) {
            $user->addRole($role);
        }
        $this->updateUser($user);

        return $user;
    }

    public function createNewAdmin($id)
    {
        $user = new Admin($id);
        $role = $this->em->getRepository('OpenLoyaltyUserBundle:Role')->findOneBy(['role' => 'ROLE_ADMIN']);
        if ($role) {
            $user->addRole($role);
        }

        return $user;
    }

    /**
     * @param User $user
     *
     * @return UserPasswordEncoderInterface
     */
    protected function getEncoder(User $user)
    {
        return $this->passwordEncoder;
    }
}
