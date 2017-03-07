<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\PointsBundle\DataFixtures\ORM;

use Broadway\ReadModel\RepositoryInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenLoyalty\Bundle\UserBundle\DataFixtures\ORM\LoadUserData;
use OpenLoyalty\Domain\Account\AccountId;
use OpenLoyalty\Domain\Account\Command\AddPoints;
use OpenLoyalty\Domain\Account\Command\ExpirePointsTransfer;
use OpenLoyalty\Domain\Account\Command\SpendPoints;
use OpenLoyalty\Domain\Account\Model\AddPointsTransfer;
use OpenLoyalty\Domain\Account\Model\SpendPointsTransfer;
use OpenLoyalty\Domain\Account\PointsTransferId;
use OpenLoyalty\Domain\Account\ReadModel\AccountDetails;
use Symfony\Bridge\Doctrine\Tests\Fixtures\ContainerAwareFixture;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadAccountsWithTransfersData.
 */
class LoadAccountsWithTransfersData extends ContainerAwareFixture implements OrderedFixtureInterface
{
    const ACCOUNT2_ID = 'e82c96cf-32a3-43bd-9034-4df343e5fd92';
    const POINTS_ID = 'e82c96cf-32a3-43bd-9034-4df343e5f111';
    const POINTS22_ID = 'e82c96cf-32a3-43bd-9034-4df343e5f211';
    const POINTS2_ID = 'e82c96cf-32a3-43bd-9034-4df343e5f222';
    const POINTS3_ID = 'e82c96cf-32a3-43bd-9034-4df343e5f333';
    const POINTS4_ID = 'e82c96cf-32a3-43bd-9034-4df343e5f433';

    public function load(ObjectManager $manager)
    {
        $commandBud = $this->container->get('broadway.command_handling.command_bus');
        $accountId = $this->getAccountIdByCustomerId(LoadUserData::TEST_USER_ID);
        $account2Id = $this->getAccountIdByCustomerId(LoadUserData::USER_USER_ID);

        $commandBud->dispatch(
            new AddPoints(new AccountId($accountId), new AddPointsTransfer(new PointsTransferId(static::POINTS_ID), 100, new \DateTime('-29 days')))
        );

        $commandBud->dispatch(
            new AddPoints(new AccountId($account2Id), new AddPointsTransfer(new PointsTransferId(static::POINTS22_ID), 100, new \DateTime('-29 days')))
        );
        $commandBud->dispatch(
            new AddPoints(new AccountId($accountId), new AddPointsTransfer(new PointsTransferId(static::POINTS4_ID), 100, new \DateTime('-29 days')))
        );
        $commandBud->dispatch(
            new AddPoints(new AccountId($accountId), new AddPointsTransfer(new PointsTransferId(static::POINTS2_ID), 100, new \DateTime('-3 days')))
        );
        $commandBud->dispatch(
            new SpendPoints(new AccountId($accountId), new SpendPointsTransfer(new PointsTransferId(static::POINTS3_ID), 100, null, false, 'Example comment'))
        );
        $commandBud->dispatch(
            new ExpirePointsTransfer(new AccountId($accountId), new PointsTransferId(static::POINTS_ID))
        );
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Get the order of this fixture.
     *
     * @return int
     */
    public function getOrder()
    {
        return 10;
    }

    /**
     * @return string
     */
    protected function getAccountIdByCustomerId($customerId)
    {
        /** @var RepositoryInterface $repo */
        $repo = $this->getContainer()->get('oloy.points.account.repository.account_details');
        $accounts = $repo->findBy(['customerId' => $customerId]);
        /** @var AccountDetails $account */
        $account = reset($accounts);
        $accountId = $account->getAccountId()->__toString();

        return $accountId;
    }
}
