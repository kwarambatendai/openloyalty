<?php

namespace OpenLoyalty\Bundle\UserBundle\EventListener;

use Broadway\CommandHandling\CommandBusInterface;
use OpenLoyalty\Bundle\UserBundle\Event\UserRegisteredWithInvitationToken;
use OpenLoyalty\Domain\Customer\Command\AttachCustomerToInvitation;
use OpenLoyalty\Domain\Customer\ReadModel\InvitationDetails;
use OpenLoyalty\Domain\Customer\ReadModel\InvitationDetailsRepository;

/**
 * Class UserRegisteredWithInvitationTokenListener.
 */
class UserRegisteredWithInvitationTokenListener
{
    /**
     * @var InvitationDetailsRepository
     */
    private $invitationDetailsRepository;

    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * UserRegisteredWithInvitationTokenListener constructor.
     *
     * @param InvitationDetailsRepository $invitationDetailsRepository
     * @param CommandBusInterface         $commandBus
     */
    public function __construct(
        InvitationDetailsRepository $invitationDetailsRepository,
        CommandBusInterface $commandBus
    ) {
        $this->invitationDetailsRepository = $invitationDetailsRepository;
        $this->commandBus = $commandBus;
    }

    public function handle(UserRegisteredWithInvitationToken $event)
    {
        $invitations = $this->invitationDetailsRepository->findByToken($event->getInvitationToken());
        if (count($invitations) > 0) {
            $invitation = reset($invitations);
            if ($invitation instanceof InvitationDetails && !$invitation->getRecipientId()) {
                $this->commandBus
                    ->dispatch(new AttachCustomerToInvitation($invitation->getInvitationId(), $event->getCustomerId()));
            }
        }
    }
}
