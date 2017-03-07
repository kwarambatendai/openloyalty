<?php

namespace OpenLoyalty\Infrastructure\Customer\SystemEvent\Listener;

use Broadway\CommandHandling\CommandBusInterface;
use Broadway\EventDispatcher\EventDispatcherInterface;
use OpenLoyalty\Domain\Account\AccountId;
use OpenLoyalty\Domain\Account\SystemEvent\AccountCreatedSystemEvent;
use OpenLoyalty\Domain\Account\SystemEvent\AvailablePointsAmountChangedSystemEvent;
use OpenLoyalty\Domain\Customer\Command\MoveCustomerToLevel;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\LevelIdProvider;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use OpenLoyalty\Domain\Level\Level;
use OpenLoyalty\Domain\Customer\LevelId as CustomerLevelId;
use OpenLoyalty\Domain\Level\LevelId;
use OpenLoyalty\Domain\Level\LevelRepository;
use OpenLoyalty\Domain\Level\Model\Reward;
use OpenLoyalty\Domain\Transaction\SystemEvent\CustomerAssignedToTransactionSystemEvent;
use OpenLoyalty\Domain\Transaction\TransactionId;
use OpenLoyalty\Infrastructure\Customer\ExcludeDeliveryCostsProvider;
use OpenLoyalty\Infrastructure\Customer\TierAssignTypeProvider;

/**
 * Class CalculateCustomerLevelListenerTest.
 */
class CalculateCustomerLevelListenerTest extends \PHPUnit_Framework_TestCase
{
    CONST LEVEL_WITH_REWARD_10_FROM_0 = '00000000-0000-0000-0000-000000000000';
    CONST LEVEL_WITH_REWARD_200_FROM_20 = '00000000-0000-0000-0000-000000000001';
    CONST LEVEL_WITH_REWARD_300_FROM_30 = '00000000-0000-0000-0000-000000000002';
    /**
     * @test
     */
    public function it_moves_customer_to_correct_level_by_transaction()
    {
        $customerId = '00000000-0000-0000-0000-000000000000';
        $levelId = new LevelId('00000000-0000-0000-0000-000000000003');
        $level = new Level($levelId, 'test', 10);

        $commandBus = $this->getMock(CommandBusInterface::class);
        $commandBus->expects($this->once())->method('dispatch')->with(
            $this->equalTo(
                new MoveCustomerToLevel(
                    new CustomerId($customerId),
                    new \OpenLoyalty\Domain\Customer\LevelId($levelId->__toString())
                )
            )
        );

        $listener = new CalculateCustomerLevelListener(
            $this->getLevelIdProvider($level),
            $this->getCustomerDetailsRepository(),
            $commandBus,
            $this->getTierTypeAssignProvider(TierAssignTypeProvider::TYPE_TRANSACTIONS),
            $this->getExcludeDeliveryCostsProvider(false),
            $this->getLevelRepository(),
            $this->getDispatcher()
        );

        $listener->handle(new CustomerAssignedToTransactionSystemEvent(
            new TransactionId('00000000-0000-0000-0000-000000000000'),
            new \OpenLoyalty\Domain\Transaction\CustomerId($customerId),
            20,
            20
        ));
    }

    /**
     * @test
     * @dataProvider testLevelsWithAssigned
     */
    public function it_moves_customer_to_correct_level_by_transaction_with_manually_assigned_level(
        CustomerLevelId $currentLevelId,
        CustomerLevelId $assignedLevel,
        $transactionAmount,
        CustomerLevelId $resultLevelId = null
    )
    {
        $levels = $this->getSampleLevels();
        $levelsRepo = $this->getLevelRepositoryWithArray($levels);


        $customerId = '00000000-0000-0000-0000-000000000000';

        $commandBus = $this->getMock(CommandBusInterface::class);
        if ($resultLevelId == null) {
            $commandBus->expects($this->never())->method('dispatch');
        } else {
            $commandBus->expects($this->once())->method('dispatch')->with(
                $this->equalTo(
                    new MoveCustomerToLevel(
                        new CustomerId($customerId),
                        $resultLevelId
                    )
                )
            );
        }

        $listener = new CalculateCustomerLevelListener(
            $this->getLevelIdProvider($levels),
            $this->getCustomerDetailsRepository($currentLevelId, $assignedLevel),
            $commandBus,
            $this->getTierTypeAssignProvider(TierAssignTypeProvider::TYPE_TRANSACTIONS),
            $this->getExcludeDeliveryCostsProvider(false),
            $levelsRepo,
            $this->getDispatcher()
        );

        $listener->handle(new CustomerAssignedToTransactionSystemEvent(
            new TransactionId('00000000-0000-0000-0000-000000000000'),
            new \OpenLoyalty\Domain\Transaction\CustomerId($customerId),
            $transactionAmount,
            $transactionAmount
        ));
    }

    /**
     * @test
     */
    public function it_moves_customer_to_correct_level_on_registration()
    {
        $customerId = '00000000-0000-0000-0000-000000000000';
        $levelId = new LevelId('00000000-0000-0000-0000-000000000003');
        $level = new Level($levelId, 'test', 0);

        $commandBus = $this->getMock(CommandBusInterface::class);
        $commandBus->expects($this->once())->method('dispatch')->with(
            $this->equalTo(
                new MoveCustomerToLevel(
                    new CustomerId($customerId),
                    new \OpenLoyalty\Domain\Customer\LevelId($levelId->__toString())
                )
            )
        );

        $listener = new CalculateCustomerLevelListener(
            $this->getLevelIdProvider($level),
            $this->getCustomerDetailsRepository(),
            $commandBus,
            $this->getTierTypeAssignProvider(TierAssignTypeProvider::TYPE_TRANSACTIONS),
            $this->getExcludeDeliveryCostsProvider(false),
            $this->getLevelRepository(),
            $this->getDispatcher()
        );

        $listener->handle(new AccountCreatedSystemEvent(
            new AccountId('00000000-0000-0000-0000-000000000000'),
            new \OpenLoyalty\Domain\Account\CustomerId($customerId)
        ));
    }

    /**
     * @test
     */
    public function it_moves_customer_to_correct_level_by_points()
    {
        $customerId = '00000000-0000-0000-0000-000000000000';
        $levelId = new LevelId('00000000-0000-0000-0000-000000000003');
        $level = new Level($levelId, 'test', 10);

        $commandBus = $this->getMock(CommandBusInterface::class);
        $commandBus->expects($this->once())->method('dispatch')->with(
            $this->equalTo(
                new MoveCustomerToLevel(
                    new CustomerId($customerId),
                    new \OpenLoyalty\Domain\Customer\LevelId($levelId->__toString())
                )
            )
        );

        $listener = new CalculateCustomerLevelListener(
            $this->getLevelIdProvider($level),
            $this->getCustomerDetailsRepository(),
            $commandBus,
            $this->getTierTypeAssignProvider(TierAssignTypeProvider::TYPE_POINTS),
            $this->getExcludeDeliveryCostsProvider(true),
            $this->getLevelRepository(),
            $this->getDispatcher()
        );
        $listener->handle(new AvailablePointsAmountChangedSystemEvent(
            new AccountId('00000000-0000-0000-0000-000000000000'),
            new \OpenLoyalty\Domain\Account\CustomerId($customerId),
            20,
            20
        ));
    }

    protected function getTierTypeAssignProvider($type)
    {
        $mock = $this->getMock(TierAssignTypeProvider::class);
        $mock->method('getType')->willReturn($type);

        return $mock;
    }

    protected function getCustomerDetailsRepository(CustomerLevelId $currentLevelId = null, CustomerLevelId $assignedLevelId = null)
    {
        $repo = $this->getMock(CustomerDetailsRepository::class);
        $repo->method('find')->with($this->isType('string'))->willReturnCallback(function($id) use ($currentLevelId, $assignedLevelId) {
            $customer = $this->getMockBuilder(CustomerDetails::class);
            $customer->disableOriginalConstructor();
            $customer = $customer->getMock();
            $customer->method('getCustomerId')->willReturn(new CustomerId($id));
            $customer->method('getLevelId')->willReturn($currentLevelId);
            $customer->method('getManuallyAssignedLevelId')->willReturn($assignedLevelId);

            return $customer;
        });

        return $repo;
    }

    protected function getLevelIdProvider($levels)
    {
        if (!is_array($levels)) {
            $levels = [$levels];
        }
        $repo = $this->getMock(LevelIdProvider::class);
        $repo->method('findLevelIdByConditionValueWithTheBiggestReward')
            ->with($this->greaterThanOrEqual(0))
            ->will($this->returnCallback(function ($conditionValue) use ($levels) {
                $current = null;
                if (count($levels) == 1) {
                    $level = reset($levels);
                    if ($level->getConditionValue() <= $conditionValue) {
                        return $level->getLevelId()->__toString();
                    } else {
                        return null;
                    }
                }
                /** @var Level $level */
                foreach ($levels as $level) {
                    if ($level->getConditionValue() <= $conditionValue
                        && (!$current || $level->getReward()->getValue() > $current->getReward()->getValue())
                    ) {
                        $current = $level;
                    }
                }

                return $current ? $current->getLevelId()->__toString() : null;
            }));

        return $repo;
    }

    protected function getExcludeDeliveryCostsProvider($returnValue)
    {
        $mock = $this->getMock(ExcludeDeliveryCostsProvider::class);
        $mock->method('areExcluded')->willReturn($returnValue);

        return $mock;
    }

    protected function getDispatcher()
    {
        $mock = $this->getMockBuilder(EventDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $mock;
    }

    protected function getLevelRepository()
    {
        $mock = $this->getMockBuilder(LevelRepository::class)
        ->disableOriginalConstructor()
        ->getMock();
        $levelId = new LevelId('00000000-0000-0000-0000-000000000003');
        $mock
            ->method('byId')
            ->will($this->returnValue(new Level($levelId, 'abcd', 20)));

        return $mock;
    }

    protected function getLevelRepositoryWithArray($levels)
    {
        $mock = $this->getMockBuilder(LevelRepository::class)
        ->disableOriginalConstructor()
        ->getMock();
        $mock
            ->method('byId')
            ->with($this->isInstanceOf(LevelId::class))
            ->will($this->returnCallback(function (LevelId $id) use ($levels) {
                if (isset($levels[$id->__toString()])) {
                    return $levels[$id->__toString()];
                }

                return null;
            }));

        return $mock;
    }

    public function testLevelsWithAssigned()
    {
        return [
            [
                new CustomerLevelId(static::LEVEL_WITH_REWARD_200_FROM_20),
                new CustomerLevelId(static::LEVEL_WITH_REWARD_200_FROM_20),
                40,
                new CustomerLevelId(static::LEVEL_WITH_REWARD_300_FROM_30),
            ],
            [
                new CustomerLevelId(static::LEVEL_WITH_REWARD_300_FROM_30),
                new CustomerLevelId(static::LEVEL_WITH_REWARD_200_FROM_20),
                0,
                new CustomerLevelId(static::LEVEL_WITH_REWARD_200_FROM_20),
            ],
            [
                new CustomerLevelId(static::LEVEL_WITH_REWARD_200_FROM_20),
                new CustomerLevelId(static::LEVEL_WITH_REWARD_200_FROM_20),
                21,
                null, // do not change level
            ],
            [
                new CustomerLevelId(static::LEVEL_WITH_REWARD_300_FROM_30),
                new CustomerLevelId(static::LEVEL_WITH_REWARD_10_FROM_0),
                21,
                new CustomerLevelId(static::LEVEL_WITH_REWARD_200_FROM_20),
            ],
        ];
    }

    /**
     * @return mixed
     */
    protected function getSampleLevels()
    {
        $level = [];
        $level[static::LEVEL_WITH_REWARD_10_FROM_0] = new Level(
            new LevelId(static::LEVEL_WITH_REWARD_10_FROM_0),
            'level_0',
            0
        );
        $level[static::LEVEL_WITH_REWARD_10_FROM_0]->setReward(new Reward('level_0_reward', 10, 'level'));

        $level[static::LEVEL_WITH_REWARD_200_FROM_20] = new Level(
            new LevelId(static::LEVEL_WITH_REWARD_200_FROM_20),
            'level_1',
            20
        );
        $level[static::LEVEL_WITH_REWARD_200_FROM_20]->setReward(new Reward('level_1_reward', 200, 'level'));

        $level[static::LEVEL_WITH_REWARD_300_FROM_30] = new Level(
            new LevelId(static::LEVEL_WITH_REWARD_300_FROM_30),
            'level_2',
            30
        );
        $level[static::LEVEL_WITH_REWARD_300_FROM_30]->setReward(new Reward('level_2_reward', 300, 'level'));

        return $level;
    }
}
