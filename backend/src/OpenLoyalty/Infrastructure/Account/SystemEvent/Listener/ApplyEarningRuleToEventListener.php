<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Infrastructure\Account\SystemEvent\Listener;

use Broadway\CommandHandling\CommandBusInterface;
use Broadway\ReadModel\RepositoryInterface;
use Broadway\UuidGenerator\UuidGeneratorInterface;
use OpenLoyalty\Domain\Account\Command\AddPoints;
use OpenLoyalty\Domain\Account\Model\AddPointsTransfer;
use OpenLoyalty\Domain\Account\PointsTransferId;
use OpenLoyalty\Domain\Account\SystemEvent\AccountCreatedSystemEvent;
use OpenLoyalty\Domain\Account\SystemEvent\AccountSystemEvents;
use OpenLoyalty\Domain\Account\SystemEvent\CustomEventOccurredSystemEvent;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerAttachedToInvitationSystemEvent;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerLoggedInSystemEvent;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerReferralSystemEvent;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerSystemEvents;
use OpenLoyalty\Domain\Customer\SystemEvent\NewsletterSubscriptionSystemEvent;
use OpenLoyalty\Domain\EarningRule\ReferralEarningRule;
use OpenLoyalty\Domain\Transaction\SystemEvent\CustomerFirstTransactionSystemEvent;
use OpenLoyalty\Domain\Transaction\SystemEvent\TransactionSystemEvents;
use OpenLoyalty\Infrastructure\Account\EarningRuleApplier;
use OpenLoyalty\Infrastructure\Account\EarningRuleLimitValidator;

/**
 * Class ApplyEarningRuleToEventListener.
 */
class ApplyEarningRuleToEventListener extends BaseApplyEarningRuleListener
{
    /**
     * @var EarningRuleLimitValidator
     */
    protected $earningRuleLimitValidator;

    public function __construct(
        CommandBusInterface $commandBus,
        RepositoryInterface $accountDetailsRepository,
        UuidGeneratorInterface $uuidGenerator,
        EarningRuleApplier $earningRuleApplier,
        EarningRuleLimitValidator $earningRuleLimitValidator = null
    ) {
        parent::__construct($commandBus, $accountDetailsRepository, $uuidGenerator, $earningRuleApplier);
        $this->earningRuleLimitValidator = $earningRuleLimitValidator;
    }

    public function onCustomEvent(CustomEventOccurredSystemEvent $event)
    {
        $result = $this->earningRuleApplier->evaluateCustomEvent($event->getEventName(), $event->getCustomerId());
        if (null == $result || $result->getPoints() <= 0) {
            return;
        }
        $account = $this->getAccountDetails($event->getCustomerId()->__toString());
        if (!$account) {
            return;
        }
        if ($this->earningRuleLimitValidator) {
            $this->earningRuleLimitValidator->validate($result->getEarningRuleId(), $event->getCustomerId());
        }

        $this->commandBus->dispatch(
            new AddPoints($account->getAccountId(), new AddPointsTransfer(
                new PointsTransferId($this->uuidGenerator->generate()),
                $result->getPoints(),
                null,
                false
            ))
        );
        $event->setEvaluationResult($result);
    }

    public function onCustomerRegistered(AccountCreatedSystemEvent $event)
    {
        $points = $this->earningRuleApplier->evaluateEvent(AccountSystemEvents::ACCOUNT_CREATED, $event->getCustomerId());
        if ($points > 0) {
            $this->commandBus->dispatch(
                new AddPoints($event->getAccountId(), new AddPointsTransfer(
                    new PointsTransferId($this->uuidGenerator->generate()),
                    $points,
                    null,
                    false
                ))
            );
        }
    }

    public function onCustomerAttachedToInvitation(CustomerAttachedToInvitationSystemEvent $event)
    {
        $this->evaluateReferral(ReferralEarningRule::EVENT_REGISTER, $event->getCustomerId()->__toString());
    }

    public function onFirstTransaction(CustomerFirstTransactionSystemEvent $event)
    {
        $points = $this->earningRuleApplier->evaluateEvent(TransactionSystemEvents::CUSTOMER_FIRST_TRANSACTION, $event->getCustomerId());
        $account = $this->getAccountDetails($event->getCustomerId()->__toString());

        if (!$account) {
            return;
        }

        if ($points > 0) {
            $this->commandBus->dispatch(
                new AddPoints($account->getAccountId(), new AddPointsTransfer(
                    new PointsTransferId($this->uuidGenerator->generate()),
                    $points,
                    null,
                    false
                ))
            );
        }

        $this->evaluateReferral(ReferralEarningRule::EVENT_FIRST_PURCHASE, $event->getCustomerId()->__toString());
    }

    public function onCustomerLogin(CustomerLoggedInSystemEvent $event)
    {
        $points = $this->earningRuleApplier->evaluateEvent(CustomerSystemEvents::CUSTOMER_LOGGED_IN, $event->getCustomerId());
        if ($points <= 0) {
            return;
        }
        $account = $this->getAccountDetails($event->getCustomerId()->__toString());

        if (!$account) {
            return;
        }

        $this->commandBus->dispatch(
            new AddPoints($account->getAccountId(), new AddPointsTransfer(
                new PointsTransferId($this->uuidGenerator->generate()),
                $points,
                null,
                false
            ))
        );
    }

//    public function onCustomerReferral(CustomerReferralSystemEvent $event)
//    {
//        $points = $this->earningRuleApplier->evaluateEvent(CustomerSystemEvents::CUSTOMER_REFERRAL);
//        if ($points <= 0) {
//            return;
//        }
//        $account = $this->getAccountDetails($event->getReferralCustomerId()->__toString());
//
//        if (!$account) {
//            return;
//        }
//
//        $this->commandBus->dispatch(
//            new AddPoints($account->getAccountId(), new AddPointsTransfer(
//                new PointsTransferId($this->uuidGenerator->generate()),
//                $points,
//                null,
//                false,
//                null,
//                sprintf('%s customer referral', $event->getCustomerId()->__toString())))
//        );
//    }

    public function onNewsletterSubscription(NewsletterSubscriptionSystemEvent $event)
    {
        $points = $this->earningRuleApplier->evaluateEvent(CustomerSystemEvents::NEWSLETTER_SUBSCRIPTION, $event->getCustomerId());
        if ($points <= 0) {
            return;
        }
        $account = $this->getAccountDetails($event->getCustomerId()->__toString());

        if (!$account) {
            return;
        }

        $this->commandBus->dispatch(
            new AddPoints($account->getAccountId(), new AddPointsTransfer(
                new PointsTransferId($this->uuidGenerator->generate()),
                $points,
                null,
                false,
                null,
                'Newsletter subscription'
                )
            )
        );
    }
}
