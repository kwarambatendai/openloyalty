<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\DemoBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenLoyalty\Bundle\SettingsBundle\Entity\StringSettingEntry;
use OpenLoyalty\Bundle\SettingsBundle\Model\Settings;
use OpenLoyalty\Bundle\UserBundle\Entity\Customer;
use OpenLoyalty\Domain\Customer\Command\RegisterCustomer;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Transaction\PosId;
use OpenLoyalty\Domain\Customer\Command\ActivateCustomer;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Faker\Factory;
use OpenLoyalty\Domain\Transaction\Command\RegisterTransaction;
use OpenLoyalty\Domain\Transaction\TransactionId;
use Symfony\Component\Yaml\Yaml;

/**
 * LoadCustomersAndTransactionsData.
 *
 * @category    DivanteOpenLoyalty
 *
 * @author      Michal Kajszczak <mkajszczak@divante.pl>
 * @copyright   Copyright (C) 2016 Divante Sp. z o.o.
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class LoadCustomersAndTransactionsData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    const USER_USERNAME = 'user@oloy.com';
    const USER_PASSWORD = 'loyalty';
    const TEST_USER_ID = '00000000-0000-474c-b092-b0dd880c07e2';
    const TEST_SELLER_ID = '00000000-0000-474c-b092-b0dd880c07e4';
    const USER_USER_ID = '00000000-0000-474c-b092-b0dd880c07e1';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->loadCustomersData($manager);
        $this->loadBaseCustomersData($manager);
    }

    /**
     * @param ObjectManager $manager
     */
    protected function loadCustomersData(ObjectManager $manager)
    {
        $settings = new Settings();
        $config = new StringSettingEntry('tierAssignType');
        $config->setValue('transactions');

        $settings->addEntry($config);
        $settingsManager = $this->container->get('ol.settings.manager');
        $settingsManager->save($settings);
        $bus = $this->container->get('broadway.command_handling.command_bus');
        $faker = Factory::create();
        $posIds = [LoadPosData::POS_ID, LoadPosData::POS2_ID];
        for ($k = 0; $k < 300; ++$k) {
            $uuid = $faker->uuid;
            $customerId = new CustomerId($uuid);

            if ($k % 2 == 0) {
                $customerData = $this->getCustomerData($faker, true);
                $transactionData = $this->getTransactionData($faker, true);
            } else {
                $customerData = $this->getCustomerData($faker);
                $transactionData = $this->getTransactionData($faker);
            }
            $command = new RegisterCustomer($customerId, $customerData);
            $bus->dispatch($command);
            $bus->dispatch(new ActivateCustomer($customerId));

            $user = new Customer($customerId);
            $user->setPlainPassword($faker->password);

            $password = $this->container->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setIsActive(true);

            $manager->persist($user);
            $manager->flush();
            $randomTransAmount = rand(1, 3);
            $transactionCustomerData = $this->getTransactionCustomerData($customerData);
            for ($i = 0; $i < $randomTransAmount; ++$i) {
                $transactionUuid = $faker->uuid;
                $bus->dispatch(
                    new RegisterTransaction(
                        new TransactionId($transactionUuid),
                        $transactionData,
                        $transactionCustomerData,
                        $this->getTransactionItems(),
                        new PosId($posIds[array_rand($posIds)])
                    )

                );
            }
        }
    }

    /**
     * @param Factory $faker
     *
     * @return array
     */
    public function getCustomerData($faker, $christmas = false)
    {
        $gender = ['male', 'female'];

        if (!$christmas) {
            $registrationDate = $faker->dateTimeBetween('-30 days');
        } else {
            $registrationDate = $faker->dateTimeBetween('1 October 2016', '23 December 2016');
        }

        return [
            'firstName' => $faker->firstName,
            'lastName' => $faker->lastName,
            'gender' => $gender[array_rand($gender)],
            'phone' => $faker->e164PhoneNumber,
            'email' => $faker->safeEmail,
            'birthDate' => date_timestamp_get($faker->dateTimeBetween('-60 years', '-10 years')),
            'createdAt' => date_timestamp_get($registrationDate),
            'company' => [
                'name' => $faker->company,
                'nip' => $faker->ean13,
            ],
            'loyaltyCardNumber' => $faker->ean8,
            'address' => [
                'street' => $faker->streetName,
                'address1' => $faker->buildingNumber,
                'city' => $faker->city,
                'country' => 'PL',
                'postal' => $faker->postcode,
                'province' => $faker->state,
            ],
            'agreement1' => true,
            'agreement2' => true,
            'agreement3' => true,
        ];
    }

    /**
     * @param Factory $faker
     *
     * @return array
     */
    public function getTransactionData($faker, $christmas = false)
    {
        if (!$christmas) {
            $purchaseDate = $faker->dateTimeThisMonth;
        } else {
            $purchaseDate = $faker->dateTimeBetween('1 November 2016', '23 December 2016');
        }
        $purchasePlaces = ['france_1', 'us_online_1', 'pos1', 'ecommerce2'];
        $transactionData = [
            'documentNumber' => $faker->ean13,
            'purchasePlace' => $purchasePlaces[array_rand($purchasePlaces)],
            'purchaseDate' => date_timestamp_get($purchaseDate),
            'documentType' => 'sell',
        ];

        return $transactionData;
    }

    /**
     * @return array
     */
    public function getTransactionItems()
    {
        $products = Yaml::parse(file_get_contents(dirname(__FILE__).'/products.yml'));

        $items = [];
        $itemsCount = rand(1, 5);
        for ($i = 0; $i < $itemsCount; ++$i) {
            $item = array_rand($products);
            $quantity = rand(1, 3);
            $itemPrice = $quantity * $products[$item]['price'];
            $items[] = [
                'sku' => [
                    'code' => $products[$item]['sku'],
                ],
                'name' => $products[$item]['name'],
                'quantity' => $quantity,
                'grossValue' => $itemPrice,
                'category' => $products[$item]['category'],
                'maker' => $products[$item]['maker'],
                'labels' => [
                    [
                        'key' => 'promotion',
                        'value' => $products[$item]['labels']['promotion'],
                    ],
                ],
            ];
        }

        return $items;
    }

    /**
     * @param array $customerData
     *
     * @return array
     */
    public function getTransactionCustomerData($customerData)
    {
        $transactionCustomerData = [
            'name' => $customerData['firstName'].' '.$customerData['lastName'],
            'email' => $customerData['email'],
            'nip' => $customerData['company']['nip'],
            'phone' => $customerData['phone'],
            'loyaltyCardNumber' => $customerData['loyaltyCardNumber'],
            'address' => $customerData['address'],
        ];

        return $transactionCustomerData;
    }

    protected function loadBaseCustomersData(ObjectManager $manager)
    {
        $bus = $this->container->get('broadway.command_handling.command_bus');

        $customerId = new CustomerId(static::USER_USER_ID);
        $command = new RegisterCustomer(
            $customerId,
            $this->getDefaultCustomerData('John', 'Doe', 'user@oloy.com', '11111')
        );
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
        return 2;
    }
}
