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
use OpenLoyaltyPlugin\SalesManagoBundle\Entity\Config;

class LoadSalesManagoConfigdata extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $config = new Config();
        $config->setSalesManagoApiEndpoint('http://www.salesmanago.pl/api');
        $config->setSalesManagoApiSecret('');
        $config->setSalesManagoOwnerEmail('');
        $config->setSalesManagoCustomerId('');
        $config->setSalesManagoApiKey('');
        $config->setSalesManagoIsActive(false);

        $manager->persist($config);
        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 0;
    }
}
