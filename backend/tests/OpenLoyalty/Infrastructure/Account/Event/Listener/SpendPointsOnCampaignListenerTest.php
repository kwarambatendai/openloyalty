<?php

namespace OpenLoyalty\Infrastructure\Account\Event\Listener;

use Broadway\CommandHandling\CommandBusInterface;
use Broadway\ReadModel\RepositoryInterface;
use Broadway\UuidGenerator\UuidGeneratorInterface;
use OpenLoyalty\Domain\Account\AccountId;
use OpenLoyalty\Domain\Account\Command\SpendPoints;
use OpenLoyalty\Domain\Account\Model\SpendPointsTransfer;
use OpenLoyalty\Domain\Account\PointsTransferId;
use OpenLoyalty\Domain\Account\ReadModel\AccountDetails;
use OpenLoyalty\Domain\Customer\CampaignId;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\Event\CampaignWasBoughtByCustomer;
use OpenLoyalty\Domain\Customer\Model\CampaignPurchase;
use OpenLoyalty\Domain\Customer\Model\Coupon;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerBoughtCampaignSystemEvent;

/**
 * Class SpendPointsOnCampaignListenerTest.
 */
class SpendPointsOnCampaignListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $uuid = '00000000-0000-0000-0000-000000000000';
    protected function getUuidGenerator()
    {
        $mock = $this->getMock(UuidGeneratorInterface::class);
        $mock->method('generate')->willReturn($this->uuid);

        return $mock;
    }

    /**
     * @test
     */
    public function it_spend_points_when_customer_bought_campaign()
    {
        $listener = new SpendPointsOnCampaignListener(
            $this->getCommandBus(
                new SpendPoints(
                    new AccountId($this->uuid),
                    new SpendPointsTransfer(
                        new PointsTransferId($this->uuid),
                        10,
                        null,
                        false,
                        'test, coupon: 123'
                    )
                )
            ),
            $this->getAccountDetailsRepository(),
            $this->getUuidGenerator()
        );
        $listener->onCustomerBoughtCampaign(new CampaignWasBoughtByCustomer(
            new CustomerId($this->uuid),
            new CampaignId($this->uuid),
            'test',
            10,
            new Coupon('123')
        ));
    }

    protected function getAccountDetailsRepository()
    {
        $account = $this->getMockBuilder(AccountDetails::class)->disableOriginalConstructor()->getMock();
        $account->method('getAccountId')->willReturn(new AccountId($this->uuid));

        $repo = $this->getMock(RepositoryInterface::class);
        $repo->method('findBy')->with($this->arrayHasKey('customerId'))->willReturn([$account]);

        return $repo;
    }


    protected function getCommandBus($expected)
    {
        $mock = $this->getMock(CommandBusInterface::class);
        $mock->method('dispatch')->with($this->equalTo($expected));

        return $mock;
    }
}
