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
use OpenLoyalty\Bundle\UserBundle\Entity\Role;

class LoadRoleData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $role = new Role('ROLE_USER');
        $manager->persist($role);
        $this->addReference('role_user', $role);

        $role = new Role('ROLE_PARTICIPANT');
        $manager->persist($role);
        $this->addReference('role_participant', $role);
        $role = new Role('ROLE_SELLER');
        $manager->persist($role);
        $this->addReference('role_seller', $role);

        $role = new Role('ROLE_ADMIN');

        $manager->persist($role);
        $this->addReference('role_admin', $role);
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
