<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Customer\Command;

use Broadway\CommandHandling\CommandHandler;
use Broadway\EventDispatcher\EventDispatcherInterface;
use OpenLoyalty\Domain\Customer\Invitation;
use OpenLoyalty\Domain\Customer\InvitationRepository;
use OpenLoyalty\Domain\Customer\Service\InvitationTokenGenerator;
use OpenLoyalty\Domain\Customer\SystemEvent\CustomerAttachedToInvitationSystemEvent;
use OpenLoyalty\Domain\Customer\SystemEvent\InvitationSystemEvents;

/**
 * Class InvitationCommandHandler.
 */
class InvitationCommandHandler extends CommandHandler
{
    /**
     * @var InvitationRepository
     */
    private $invitationRepository;

    /**
     * @var InvitationTokenGenerator
     */
    private $tokenGenerator;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * InvitationCommandHandler constructor.
     *
     * @param InvitationRepository     $invitationRepository
     * @param InvitationTokenGenerator $tokenGenerator
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        InvitationRepository $invitationRepository,
        InvitationTokenGenerator $tokenGenerator,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->invitationRepository = $invitationRepository;
        $this->tokenGenerator = $tokenGenerator;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function handleCreateInvitation(CreateInvitation $command)
    {
        $token = $this->tokenGenerator->generate($command->getReferrerId(), $command->getRecipientEmail());
        $invitation = Invitation::createInvitation(
            $command->getInvitationId(),
            $command->getReferrerId(),
            $command->getRecipientEmail(),
            $token
        );

        $this->invitationRepository->save($invitation);
    }

    public function handleAttachCustomerToInvitation(AttachCustomerToInvitation $command)
    {
        /** @var Invitation $invitation */
        $invitation = $this->invitationRepository->load($command->getInvitationId());
        $invitation->attachCustomer($command->getCustomerId());
        $this->invitationRepository->save($invitation);

        $this->eventDispatcher->dispatch(
            InvitationSystemEvents::CUSTOMER_ATTACHED_TO_INVITATION,
            [new CustomerAttachedToInvitationSystemEvent($command->getCustomerId(), $command->getInvitationId())]
        );
    }

    public function handleInvitedCustomerMadePurchase(InvitedCustomerMadePurchase $command)
    {
        /** @var Invitation $invitation */
        $invitation = $this->invitationRepository->load($command->getInvitationId());
        $invitation->purchaseMade();
        $this->invitationRepository->save($invitation);
    }
}
