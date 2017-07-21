<?php

namespace OpenLoyalty\Domain\Customer\ReadModel;

use Broadway\ReadModel\Projector;
use OpenLoyalty\Domain\Customer\Event\CustomerWasAttachedToInvitation;
use OpenLoyalty\Domain\Customer\Event\InvitationWasCreated;
use OpenLoyalty\Domain\Customer\Event\PurchaseWasMadeForThisInvitation;
use OpenLoyalty\Domain\Customer\InvitationId;

/**
 * Class InvitationDetailsProjector.
 */
class InvitationDetailsProjector extends Projector
{
    /**
     * @var CustomerDetailsRepository
     */
    private $customerDetailsRepository;

    /**
     * @var InvitationDetailsRepository
     */
    private $invitationDetailsRepository;

    /**
     * InvitationDetailsProjector constructor.
     *
     * @param CustomerDetailsRepository   $customerDetailsRepository
     * @param InvitationDetailsRepository $invitationDetailsRepository
     */
    public function __construct(
        CustomerDetailsRepository $customerDetailsRepository,
        InvitationDetailsRepository $invitationDetailsRepository
    ) {
        $this->customerDetailsRepository = $customerDetailsRepository;
        $this->invitationDetailsRepository = $invitationDetailsRepository;
    }

    public function applyInvitationWasCreated(InvitationWasCreated $event)
    {
        $readModel = $this->getReadModel($event->getInvitationId());
        if (!$readModel) {
            $customer = $this->customerDetailsRepository->find($event->getReferrerId()->__toString());
            if (!$customer instanceof CustomerDetails) {
                return;
            }

            $readModel = new InvitationDetails(
                $event->getInvitationId(),
                $event->getReferrerId(),
                $customer->getEmail(),
                $customer->getFirstName().' '.$customer->getLastName(),
                $event->getRecipientEmail(),
                $event->getToken()
            );
        }

        $this->invitationDetailsRepository->save($readModel);
    }

    public function applyCustomerWasAttachedToInvitation(CustomerWasAttachedToInvitation $event)
    {
        $readModel = $this->getReadModel($event->getInvitationId());
        if (!$readModel) {
            return;
        }
        $name = '';
        $customer = $this->customerDetailsRepository->find($event->getCustomerId()->__toString());
        if ($customer instanceof CustomerDetails) {
            $name = $customer->getFirstName().' '.$customer->getLastName();
        }

        $readModel->updateRecipientData($event->getCustomerId(), $name);

        $this->invitationDetailsRepository->save($readModel);
    }

    public function applyPurchaseWasMadeForThisInvitation(PurchaseWasMadeForThisInvitation $event)
    {
        $readModel = $this->getReadModel($event->getInvitationId());
        if (!$readModel) {
            return;
        }

        $readModel->madePurchase();

        $this->invitationDetailsRepository->save($readModel);
    }

    /**
     * @param InvitationId $invitationId
     *
     * @return InvitationDetails|null
     */
    private function getReadModel(InvitationId $invitationId)
    {
        $readModel = $this->invitationDetailsRepository->find($invitationId->__toString());

        return $readModel;
    }
}
