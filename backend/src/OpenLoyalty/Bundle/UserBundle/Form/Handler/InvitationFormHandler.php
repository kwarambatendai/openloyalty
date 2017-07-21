<?php

namespace OpenLoyalty\Bundle\UserBundle\Form\Handler;

use Broadway\CommandHandling\CommandBusInterface;
use Broadway\UuidGenerator\UuidGeneratorInterface;
use OpenLoyalty\Bundle\UserBundle\Service\EmailProvider;
use OpenLoyalty\Domain\Customer\Command\CreateInvitation;
use OpenLoyalty\Domain\Customer\InvitationId;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use OpenLoyalty\Domain\Customer\ReadModel\InvitationDetails;
use OpenLoyalty\Domain\Customer\ReadModel\InvitationDetailsRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class InvitationFormHandler.
 */
class InvitationFormHandler
{
    /**
     * @var CommandBusInterface
     */
    protected $commandBus;

    /**
     * @var InvitationDetailsRepository
     */
    protected $invitationDetailsRepository;

    /**
     * @var CustomerDetailsRepository
     */
    protected $customerDetailsRepository;

    /**
     * @var UuidGeneratorInterface
     */
    protected $uuidGenerator;

    /**
     * @var EmailProvider
     */
    protected $emailProvider;

    /**
     * InvitationFormHandler constructor.
     *
     * @param CommandBusInterface         $commandBus
     * @param InvitationDetailsRepository $invitationDetailsRepository
     * @param CustomerDetailsRepository   $customerDetailsRepository
     * @param UuidGeneratorInterface      $uuidGenerator
     * @param EmailProvider               $emailProvider
     */
    public function __construct(
        CommandBusInterface $commandBus,
        InvitationDetailsRepository $invitationDetailsRepository,
        CustomerDetailsRepository $customerDetailsRepository,
        UuidGeneratorInterface $uuidGenerator,
        EmailProvider $emailProvider
    ) {
        $this->commandBus = $commandBus;
        $this->invitationDetailsRepository = $invitationDetailsRepository;
        $this->customerDetailsRepository = $customerDetailsRepository;
        $this->uuidGenerator = $uuidGenerator;
        $this->emailProvider = $emailProvider;
    }

    public function onSuccess(CustomerDetails $currentCustomer, FormInterface $form)
    {
        // if customer exists or if there is an invitation from current customer to this email -> return
        $recipientEmail = $form->get('recipientEmail')->getData();

        if ($this->doNotCreateInvitation($currentCustomer, $recipientEmail)) {
            return new Response();
        }

        $invitationId = new InvitationId($this->uuidGenerator->generate());
        $this->commandBus->dispatch(new CreateInvitation(
            $invitationId,
            $currentCustomer->getCustomerId(),
            $recipientEmail
        ));

        $invitationDetails = $this->invitationDetailsRepository->find($invitationId->__toString());

        if (!$invitationDetails instanceof InvitationDetails) {
            return new Response();
        }

        $this->emailProvider->invitationEmail($invitationDetails);
    }

    protected function doNotCreateInvitation(CustomerDetails $currentCustomer, $recipientEmail)
    {
        if (count($this->customerDetailsRepository->findOneByCriteria(['email' => $recipientEmail], 1)) > 0) {
            return true;
        }

        if ($q = count($this->invitationDetailsRepository->findByParametersPaginated([
            'recipientEmail' => $recipientEmail,
            'referrerId' => $currentCustomer->getCustomerId()->__toString(),
        ], true)) > 0) {
            return true;
        }

        return false;
    }
}
