<?php

namespace OpenLoyalty\Infrastructure\Account\SytemEvent\Listener;

use OpenLoyalty\Domain\Account\AccountId;
use OpenLoyalty\Domain\Account\Command\AddPoints;
use OpenLoyalty\Domain\Account\Model\AddPointsTransfer;
use OpenLoyalty\Domain\Account\PointsTransferId;
use OpenLoyalty\Domain\Account\SystemEvent\AccountCreatedSystemEvent;
use OpenLoyalty\Domain\Account\SystemEvent\CustomEventOccurredSystemEvent;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerLoggedInSystemEvent;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerReferralSystemEvent;
use OpenLoyalty\Domain\Customer\SystemEvent\NewsletterSubscriptionSystemEvent;
use OpenLoyalty\Domain\Transaction\CustomerId;
use OpenLoyalty\Domain\Transaction\SystemEvent\CustomerFirstTransactionSystemEvent;
use OpenLoyalty\Domain\Transaction\TransactionId;
use OpenLoyalty\Infrastructure\Account\SystemEvent\Listener\ApplyEarningRuleToEventListener;


/**
 * Class ApplyEarningRuleToEventListenerTest.
 */
class ApplyEarningRuleToEventListenerTest extends BaseApplyEarningRuleListenerTest
{
    /**
     * @test
     */
    public function it_adds_points_on_registration()
    {
        $accountId = new AccountId($this->uuid);
        $expected = new AddPoints($accountId, new AddPointsTransfer(
            new PointsTransferId($this->uuid),
            10,
            null,
            false
        ));

        $listener = new ApplyEarningRuleToEventListener(
            $this->getCommandBus($expected),
            $this->getAccountDetailsRepository(),
            $this->getUuidGenerator(),
            $this->getApplierForEvent(10)
        );

        $listener->onCustomerRegistered(new AccountCreatedSystemEvent($accountId));
    }

    /**
     * @test
     */
    public function it_adds_points_on_first_transaction()
    {
        $accountId = new AccountId($this->uuid);
        $expected = new AddPoints($accountId, new AddPointsTransfer(
            new PointsTransferId($this->uuid),
            10,
            null,
            false
        ));

        $listener = new ApplyEarningRuleToEventListener(
            $this->getCommandBus($expected),
            $this->getAccountDetailsRepository(),
            $this->getUuidGenerator(),
            $this->getApplierForEvent(10)
        );

        $listener->onFirstTransaction(new CustomerFirstTransactionSystemEvent(new TransactionId($this->uuid), new CustomerId($this->uuid)));
    }

    /**
     * @test
     */
    public function it_adds_points_on_login()
    {
        $accountId = new AccountId($this->uuid);
        $expected = new AddPoints($accountId, new AddPointsTransfer(
            new PointsTransferId($this->uuid),
            10,
            null,
            false
        ));

        $listener = new ApplyEarningRuleToEventListener(
            $this->getCommandBus($expected),
            $this->getAccountDetailsRepository(),
            $this->getUuidGenerator(),
            $this->getApplierForEvent(10)
        );

        $listener->onCustomerLogin(new CustomerLoggedInSystemEvent(new \OpenLoyalty\Domain\Customer\CustomerId($this->uuid)));
    }

//    /**
//     * @test
//     */
//    public function it_adds_points_on_customer_referral()
//    {
//        $accountId = new AccountId($this->uuid);
//        $expected = new AddPoints($accountId, new AddPointsTransfer(
//            new PointsTransferId($this->uuid),
//            100,
//            null,
//            false,
//            null,
//            "$this->uuid customer referral"
//        ));
//
//        $listener = new ApplyEarningRuleToEventListener(
//            $this->getCommandBus($expected),
//            $this->getUuidGenerator(),
//            $this->getApplierForEvent(100),
//            $this->getAccountDetailsRepository()
//        );
//
//        $customerId = new \OpenLoyalty\Domain\Customer\CustomerId($this->uuid);
//        $listener->onCustomerReferral(new CustomerReferralSystemEvent($customerId, $customerId));
//    }

    /**
     * @test
     */
    public function it_adds_points_on_newsletter_subscription()
    {
        $accountId = new AccountId($this->uuid);
        $expected = new AddPoints($accountId, new AddPointsTransfer(
            new PointsTransferId($this->uuid),
            100,
            null,
            false,
            null,
            "Newsletter subscription"
        ));

        $listener = new ApplyEarningRuleToEventListener(
            $this->getCommandBus($expected),
            $this->getAccountDetailsRepository(),
            $this->getUuidGenerator(),
            $this->getApplierForEvent(100)
        );

        $customerId = new \OpenLoyalty\Domain\Customer\CustomerId($this->uuid);
        $listener->onNewsletterSubscription(new NewsletterSubscriptionSystemEvent($customerId));
    }

    /**
     * @test
     */
    public function it_adds_points_on_custom_event()
    {
        $accountId = new AccountId($this->uuid);
        $expected = new AddPoints($accountId, new AddPointsTransfer(
            new PointsTransferId($this->uuid),
            100
        ));

        $listener = new ApplyEarningRuleToEventListener(
            $this->getCommandBus($expected),
            $this->getAccountDetailsRepository(),
            $this->getUuidGenerator(),
            $this->getApplierForEvent(100)
        );

        $customerId = new \OpenLoyalty\Domain\Account\CustomerId($this->uuid);
        $listener->onCustomEvent(new CustomEventOccurredSystemEvent($customerId, 'facebook_like'));
    }
}
