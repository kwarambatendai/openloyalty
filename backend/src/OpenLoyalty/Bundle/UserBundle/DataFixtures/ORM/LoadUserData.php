<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenLoyalty\Bundle\LevelBundle\DataFixtures\ORM\LoadLevelData;
use OpenLoyalty\Bundle\PosBundle\DataFixtures\ORM\LoadPosData;
use OpenLoyalty\Bundle\UserBundle\Entity\Admin;
use OpenLoyalty\Bundle\UserBundle\Entity\Customer;
use OpenLoyalty\Bundle\UserBundle\Entity\Seller;
use OpenLoyalty\Domain\Customer\Command\ActivateCustomer;
use OpenLoyalty\Domain\Customer\Command\MoveCustomerToLevel;
use OpenLoyalty\Domain\Customer\Command\RegisterCustomer;
use OpenLoyalty\Domain\Customer\Command\UpdateCustomerAddress;
use OpenLoyalty\Domain\Customer\Command\UpdateCustomerLoyaltyCardNumber;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\LevelId;
use OpenLoyalty\Domain\Seller\Command\ActivateSeller;
use OpenLoyalty\Domain\Seller\Command\RegisterSeller;
use OpenLoyalty\Domain\Seller\PosId;
use OpenLoyalty\Domain\Seller\SellerId;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    const ADMIN_USERNAME = 'admin';
    const ADMIN_PASSWORD = 'open';
    const USER_USERNAME = 'user@oloy.com';
    const USER_PASSWORD = 'loyalty';
    const ADMIN_ID = '22200000-0000-474c-b092-b0dd880c07e2';
    const TEST_USER_ID = '00000000-0000-474c-b092-b0dd880c07e2';
    const USER_USER_ID = '00000000-0000-474c-b092-b0dd880c07e1';
    const TEST_SELLER_ID = '00000000-0000-474c-b092-b0dd880c07e4';
    const TEST_SELLER2_ID = '00000000-0000-474c-b092-b0dd880c07e5';

    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = new Admin(self::ADMIN_ID);
        $user->setPlainPassword(static::ADMIN_PASSWORD);
        $user->setEmail('admin@oloy.com');
        $password = $this->container->get('security.password_encoder')
            ->encodePassword($user, $user->getPlainPassword());

        $user->addRole($this->getReference('role_admin'));
        $user->setUsername($this::ADMIN_USERNAME);
        $user->setPassword($password);
        $user->setIsActive(true);

        $manager->persist($user);

        $this->addReference('user-admin', $user);

        $manager->flush();

        $this->loadCustomersData($manager);
        $this->loadSeller($manager);
    }

    protected function loadSeller(ObjectManager $manager)
    {
        $bus = $this->container->get('broadway.command_handling.command_bus');

        $bus->dispatch(
            new RegisterSeller(
                new SellerId(self::TEST_SELLER_ID),
                [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'email' => 'john@doe.com',
                    'phone' => '0000000011',
                    'posId' => new PosId(LoadPosData::POS_ID),
                ]
            )
        );
        $bus->dispatch(new ActivateSeller(new SellerId(self::TEST_SELLER_ID)));
        $user = new Seller(new SellerId(self::TEST_SELLER_ID));
        $user->setEmail('john@doe.com');
        $user->setIsActive(true);
        $user->addRole($this->getReference('role_seller'));
        $user->setPlainPassword('open');
        $this->container->get('oloy.user.user_manager')->updateUser($user);

        $bus->dispatch(
            new RegisterSeller(
                new SellerId(self::TEST_SELLER2_ID),
                [
                    'firstName' => 'John2',
                    'lastName' => 'Doe2',
                    'email' => 'john2@doe2.com',
                    'phone' => '0000000011',
                    'posId' => new PosId(LoadPosData::POS2_ID),
                ]
            )
        );
        $bus->dispatch(new ActivateSeller(new SellerId(self::TEST_SELLER2_ID)));
        $user = new Seller(new SellerId(self::TEST_SELLER2_ID));
        $user->setEmail('john2@doe2.com');
        $user->setIsActive(true);
        $user->addRole($this->getReference('role_seller'));
        $user->setPlainPassword('open');
        $this->container->get('oloy.user.user_manager')->updateUser($user);
    }

    protected function loadCustomersData(ObjectManager $manager)
    {
        $bus = $this->container->get('broadway.command_handling.command_bus');

        $customerId = new CustomerId(static::USER_USER_ID);
        $command = new RegisterCustomer($customerId, $this->getDefaultCustomerData('John', 'Doe', 'user@oloy.com', '11111'));

        $bus->dispatch($command);
        $bus->dispatch(new ActivateCustomer($customerId));

        $user = new Customer($customerId);
        $user->setPlainPassword($this::USER_PASSWORD);

        $password = $this->container->get('security.password_encoder')
            ->encodePassword($user, $user->getPlainPassword());

        $user->addRole($this->getReference('role_participant'));
        $user->setPassword($password);
        $user->setIsActive(true);

        $user->setEmail('user@oloy.com');
        $manager->persist($user);
        $this->addReference('user-1', $user);

        $customerId = new CustomerId(self::TEST_USER_ID);
        $command = new RegisterCustomer($customerId, $this->getDefaultCustomerData('Jane', 'Doe', 'user-temp@oloy.com', '111112222'));
        $bus->dispatch($command);
        $bus->dispatch(new UpdateCustomerAddress($customerId, [
            'street' => 'Bagno',
            'address1' => '1',
            'postal' => '00-000',
            'city' => 'Warszawa',
            'province' => 'Mazowieckie',
            'country' => 'PL',
        ]));
        $bus->dispatch(new UpdateCustomerLoyaltyCardNumber($customerId, '0000'));
        $bus->dispatch(new MoveCustomerToLevel($customerId, new LevelId(LoadLevelData::LEVEL_ID)));
        $bus->dispatch(new ActivateCustomer($customerId));

        $user = new Customer($customerId);
        $user->setPlainPassword($this::USER_PASSWORD);

        $password = $this->container->get('security.password_encoder')
            ->encodePassword($user, $user->getPlainPassword());

        $user->addRole($this->getReference('role_participant'));
        $user->setPassword($password);
        $user->setIsActive(true);

        $user->setEmail('user-temp@oloy.com');
        $user->setTemporaryPasswordSetAt(new \DateTime());

        $manager->persist($user);

        $manager->flush();
    }

    public static function getDefaultCustomerData($firstName, $lastName, $email, $phone = '00000')
    {
        return [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'gender' => 'male',
            'phone' => $phone,
            'email' => $email,
            'birthDate' => 653011200,
            'createdAt' => 1470646394,
            'company' => [
                'name' => 'test',
                'nip' => 'nip',
            ],
            'loyaltyCardNumber' => '000000',
            'address' => [
                'street' => 'Dmowskiego',
                'address1' => '21',
                'city' => 'Wrocław',
                'country' => 'pl',
                'postal' => '50-300',
                'province' => 'Dolnośląskie',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
