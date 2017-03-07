<?php

namespace OpenLoyalty\Infrastructure\Account\SytemEvent\Listener;

use Broadway\CommandHandling\CommandBusInterface;
use Broadway\ReadModel\RepositoryInterface;
use Broadway\UuidGenerator\UuidGeneratorInterface;
use OpenLoyalty\Domain\Account\AccountId;
use OpenLoyalty\Domain\Account\ReadModel\AccountDetails;
use OpenLoyalty\Domain\Account\SystemEvent\AccountSystemEvents;
use OpenLoyalty\Domain\Account\TransactionId;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerSystemEvents;
use OpenLoyalty\Domain\Transaction\SystemEvent\TransactionSystemEvents;
use OpenLoyalty\Infrastructure\Account\EarningRuleApplier;

/**
 * Class BaseApplyEarningRuleListenerTest.
 */
abstract class BaseApplyEarningRuleListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $uuid = '00000000-0000-0000-0000-000000000000';
    protected function getUuidGenerator()
    {
        $mock = $this->getMock(UuidGeneratorInterface::class);
        $mock->method('generate')->willReturn($this->uuid);

        return $mock;
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

    protected function getApplierForEvent($returnValue)
    {
        $mock = $this->getMock(EarningRuleApplier::class);
        $mock->method('evaluateEvent')->with($this->logicalOr(
            $this->equalTo(AccountSystemEvents::ACCOUNT_CREATED),
            $this->equalTo(TransactionSystemEvents::CUSTOMER_FIRST_TRANSACTION),
            $this->equalTo(CustomerSystemEvents::CUSTOMER_LOGGED_IN),
            $this->equalTo(CustomerSystemEvents::CUSTOMER_REFERRAL),
            $this->equalTo(CustomerSystemEvents::NEWSLETTER_SUBSCRIPTION)
        ))->willReturn($returnValue);

        return $mock;
    }

    protected function getApplierForTransaction($returnValue)
    {
        $mock = $this->getMock(EarningRuleApplier::class);
        $mock->method('evaluateTransaction')->with($this->isInstanceOf(TransactionId::class))
            ->willReturn($returnValue);

        return $mock;
    }
}
