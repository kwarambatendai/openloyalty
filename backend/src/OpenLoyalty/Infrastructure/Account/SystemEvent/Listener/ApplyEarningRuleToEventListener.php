<?php

namespace OpenLoyalty\Infrastructure\Account\SystemEvent\Listener;

use Broadway\CommandHandling\CommandBusInterface;
use Broadway\ReadModel\RepositoryInterface;
use Broadway\UuidGenerator\UuidGeneratorInterface;
use OpenLoyalty\Domain\Account\Command\AddPoints;
use OpenLoyalty\Domain\Account\Model\AddPointsTransfer;
use OpenLoyalty\Domain\Account\PointsTransferId;
use OpenLoyalty\Domain\Account\ReadModel\AccountDetails;
use OpenLoyalty\Domain\Account\SystemEvent\AccountCreatedSystemEvent;
use OpenLoyalty\Domain\Account\SystemEvent\AccountSystemEvents;
use OpenLoyalty\Domain\Account\SystemEvent\CustomEventOccurredSystemEvent;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerLoggedInSystemEvent;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerReferralSystemEvent;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerSystemEvents;
use OpenLoyalty\Domain\Customer\SystemEvent\NewsletterSubscriptionSystemEvent;
use OpenLoyalty\Domain\Transaction\CustomerId;
use OpenLoyalty\Domain\Transaction\SystemEvent\CustomerFirstTransactionSystemEvent;
use OpenLoyalty\Domain\Transaction\SystemEvent\TransactionSystemEvents;
use OpenLoyalty\Infrastructure\Account\EarningRuleApplier;
use OpenLoyalty\Infrastructure\Account\EarningRuleLimitValidator;

/**
 * Class ApplyEarningRuleToEventListener.
 */
class ApplyEarningRuleToEventListener
{
    /**
     * @var CommandBusInterface
     */
    protected $commandBus;

    /**
     * @var UuidGeneratorInterface
     */
    protected $uuidGenerator;

    /**
     * @var EarningRuleApplier
     */
    protected $earningRuleApplier;

    /**
     * @var RepositoryInterface
     */
    protected $accountDetailsRepository;

    /**
     * @var EarningRuleLimitValidator
     */
    protected $earningRuleLimitValidator;

    /**
     * ApplyEarningRuleToEventListener constructor.
     *
     * @param CommandBusInterface       $commandBus
     * @param UuidGeneratorInterface    $uuidGenerator
     * @param EarningRuleApplier        $earningRuleApplier
     * @param RepositoryInterface       $accountDetailsRepository
     * @param EarningRuleLimitValidator $earningRuleLimitValidator
     */
    public function __construct(
        CommandBusInterface $commandBus,
        UuidGeneratorInterface $uuidGenerator,
        EarningRuleApplier $earningRuleApplier,
        RepositoryInterface $accountDetailsRepository,
        EarningRuleLimitValidator $earningRuleLimitValidator = null
    ) {
        $this->commandBus = $commandBus;
        $this->uuidGenerator = $uuidGenerator;
        $this->earningRuleApplier = $earningRuleApplier;
        $this->accountDetailsRepository = $accountDetailsRepository;
        $this->earningRuleLimitValidator = $earningRuleLimitValidator;
    }

    public function onCustomEvent(CustomEventOccurredSystemEvent $event)
    {
        $result = $this->earningRuleApplier->evaluateCustomEvent($event->getEventName());
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
        $points = $this->earningRuleApplier->evaluateEvent(AccountSystemEvents::ACCOUNT_CREATED);
        if ($points <= 0) {
            return;
        }

        $this->commandBus->dispatch(
            new AddPoints($event->getAccountId(), new AddPointsTransfer(
                new PointsTransferId($this->uuidGenerator->generate()),
                $points,
                null,
                false
            ))
        );
    }

    public function onFirstTransaction(CustomerFirstTransactionSystemEvent $event)
    {
        $points = $this->earningRuleApplier->evaluateEvent(TransactionSystemEvents::CUSTOMER_FIRST_TRANSACTION);
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

    public function onCustomerLogin(CustomerLoggedInSystemEvent $event)
    {
        $points = $this->earningRuleApplier->evaluateEvent(CustomerSystemEvents::CUSTOMER_LOGGED_IN);
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

    public function onCustomerReferral(CustomerReferralSystemEvent $event)
    {
        $points = $this->earningRuleApplier->evaluateEvent(CustomerSystemEvents::CUSTOMER_REFERRAL);
        if ($points <= 0) {
            return;
        }
        $account = $this->getAccountDetails($event->getReferralCustomerId()->__toString());

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
                sprintf('%s customer referral', $event->getCustomerId()->__toString())))
        );
    }

    public function onNewsletterSubscription(NewsletterSubscriptionSystemEvent $event)
    {
        $points = $this->earningRuleApplier->evaluateEvent(CustomerSystemEvents::NEWSLETTER_SUBSCRIPTION);
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

    /**
     * @param string $customerId
     *
     * @return null|AccountDetails
     */
    protected function getAccountDetails($customerId)
    {
        $accounts = $this->accountDetailsRepository->findBy(['customerId' => $customerId]);
        if (count($accounts) == 0) {
            return;
        }
        /** @var AccountDetails $account */
        $account = reset($accounts);

        if (!$account instanceof AccountDetails) {
            return;
        }

        return $account;
    }
}
