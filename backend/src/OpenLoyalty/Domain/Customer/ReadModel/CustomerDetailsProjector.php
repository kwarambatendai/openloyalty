<?php

namespace OpenLoyalty\Domain\Customer\ReadModel;

use Broadway\ReadModel\Projector;
use OpenLoyalty\Domain\Customer\Event\CampaignUsageWasChanged;
use OpenLoyalty\Domain\Customer\Event\CampaignWasBoughtByCustomer;
use OpenLoyalty\Domain\Customer\Event\CustomerDetailsWereUpdated;
use OpenLoyalty\Domain\Customer\Event\CustomerWasActivated;
use OpenLoyalty\Domain\Customer\Event\CustomerWasDeactivated;
use OpenLoyalty\Domain\Customer\Event\PosWasAssignedToCustomer;
use OpenLoyalty\Domain\Customer\Model\Address;
use OpenLoyalty\Domain\Customer\Model\CampaignPurchase;
use OpenLoyalty\Domain\Customer\Model\Gender;
use OpenLoyalty\Domain\Customer\Event\CustomerAddressWasUpdated;
use OpenLoyalty\Domain\Customer\Event\CustomerCompanyDetailsWereUpdated;
use OpenLoyalty\Domain\Customer\Event\CustomerLoyaltyCardNumberWasUpdated;
use OpenLoyalty\Domain\Customer\Event\CustomerWasRegistered;
use OpenLoyalty\Domain\Customer\Model\Company;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\TransactionId;
use OpenLoyalty\Domain\Transaction\Event\CustomerWasAssignedToTransaction;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetails;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetailsRepository;
use OpenLoyalty\Domain\Transaction\Transaction;

/**
 * Class CustomerDetailsProjector.
 */
class CustomerDetailsProjector extends Projector
{
    private $repository;

    /**
     * @var TransactionDetailsRepository
     */
    private $transactionDetailsRepository;

    /**
     * CustomerDetailsProjector constructor.
     *
     * @param                              $repository
     * @param TransactionDetailsRepository $transactionDetailsRepository
     */
    public function __construct($repository, TransactionDetailsRepository $transactionDetailsRepository)
    {
        $this->repository = $repository;
        $this->transactionDetailsRepository = $transactionDetailsRepository;
    }

    protected function applyCustomerWasRegistered(CustomerWasRegistered $event)
    {
        /** @var CustomerDetails $readModel */
        $readModel = $this->getReadModel($event->getCustomerId());

        $data = $event->getCustomerData();
        $data = $readModel->resolveOptions($data);
        $readModel->setFirstName($data['firstName']);
        $readModel->setLastName($data['lastName']);
        if (!empty($data['phone'])) {
            $readModel->setPhone($data['phone']);
        }
        $readModel->setEmail($data['email']);
        if (!empty($data['gender'])) {
            $readModel->setGender(new Gender($data['gender']));
        }
        if (!empty($data['birthDate'])) {
            $readModel->setBirthDate($data['birthDate']);
        }
        if (isset($data['agreement1'])) {
            $readModel->setAgreement1($data['agreement1']);
        }
        if (isset($data['agreement2'])) {
            $readModel->setAgreement2($data['agreement2']);
        }
        if (isset($data['agreement3'])) {
            $readModel->setAgreement3($data['agreement3']);
        }
        $readModel->setUpdatedAt($event->getUpdateAt());
        $readModel->setCreatedAt($data['createdAt']);

        $this->repository->save($readModel);
    }

    protected function applyCustomerDetailsWereUpdated(CustomerDetailsWereUpdated $event)
    {
        /** @var CustomerDetails $readModel */
        $readModel = $this->getReadModel($event->getCustomerId());

        $data = $event->getCustomerData();
        if (!empty($data['firstName'])) {
            $readModel->setFirstName($data['firstName']);
        }
        if (!empty($data['lastName'])) {
            $readModel->setLastName($data['lastName']);
        }
        if (isset($data['phone'])) {
            $readModel->setPhone($data['phone']);
        }
        if (isset($data['email'])) {
            $readModel->setEmail($data['email']);
        }
        if (!empty($data['gender'])) {
            $readModel->setGender(new Gender($data['gender']));
        }
        if (!empty($data['birthDate'])) {
            $readModel->setBirthDate($data['birthDate']);
        }

        if (isset($data['agreement1'])) {
            $readModel->setAgreement1($data['agreement1']);
        }
        if (isset($data['agreement2'])) {
            $readModel->setAgreement2($data['agreement2']);
        }
        if (isset($data['agreement3'])) {
            $readModel->setAgreement3($data['agreement3']);
        }
        $readModel->setUpdatedAt($event->getUpdateAt());

        $this->repository->save($readModel);
    }

    protected function applyCustomerAddressWasUpdated(CustomerAddressWasUpdated $event)
    {
        /** @var CustomerDetails $readModel */
        $readModel = $this->getReadModel($event->getCustomerId());
        $readModel->setAddress(Address::fromData($event->getAddressData()));
        $readModel->setUpdatedAt($event->getUpdateAt());

        $this->repository->save($readModel);
    }

    protected function applyCustomerCompanyDetailsWereUpdated(CustomerCompanyDetailsWereUpdated $event)
    {
        /** @var CustomerDetails $readModel */
        $readModel = $this->getReadModel($event->getCustomerId());
        $companyData = $event->getCompanyData();
        if (!$companyData || count($companyData) == 0) {
            $readModel->setCompany(null);
        } else {
            $readModel->setCompany(new Company($companyData['name'], $event->getCompanyData()['nip']));
        }
        $readModel->setUpdatedAt($event->getUpdateAt());

        $this->repository->save($readModel);
    }

    protected function applyCustomerLoyaltyCardNumberWasUpdated(CustomerLoyaltyCardNumberWasUpdated $event)
    {
        /** @var CustomerDetails $readModel */
        $readModel = $this->getReadModel($event->getCustomerId());
        $readModel->setLoyaltyCardNumber($event->getCardNumber());
        $readModel->setUpdatedAt($event->getUpdateAt());

        $this->repository->save($readModel);
    }

    protected function applyPosWasAssignedToCustomer(PosWasAssignedToCustomer $event)
    {
        /** @var CustomerDetails $readModel */
        $readModel = $this->getReadModel($event->getCustomerId());
        $readModel->setPosId($event->getPosId());
        $readModel->setUpdatedAt($event->getUpdateAt());

        $this->repository->save($readModel);
    }

    protected function applyCampaignWasBoughtByCustomer(CampaignWasBoughtByCustomer $event)
    {
        /** @var CustomerDetails $readModel */
        $readModel = $this->getReadModel($event->getCustomerId());
        $readModel->addCampaignPurchase(new CampaignPurchase($event->getCreatedAt(), $event->getCostInPoints(), $event->getCampaignId(), $event->getCoupon()));

        $this->repository->save($readModel);
    }

    protected function applyCampaignUsageWasChanged(CampaignUsageWasChanged $event)
    {
        /** @var CustomerDetails $readModel */
        $readModel = $this->getReadModel($event->getCustomerId());
        $campaignId = $event->getCampaignId()->__toString();
        $coupon = $event->getCoupon()->getCode();

        foreach ($readModel->getCampaignPurchases() as $purchase) {
            if ($purchase->getCampaignId()->__toString() == $campaignId && $purchase->getCoupon()->getCode() == $coupon) {
                $purchase->setUsed($event->isUsed());
                $this->repository->save($readModel);

                return;
            }
        }
    }

    protected function applyCustomerWasDeactivated(CustomerWasDeactivated $event)
    {
        /** @var CustomerDetails $readModel */
        $readModel = $this->getReadModel($event->getCustomerId());
        $readModel->setActive(false);
        $this->repository->save($readModel);
    }

    protected function applyCustomerWasActivated(CustomerWasActivated $event)
    {
        /** @var CustomerDetails $readModel */
        $readModel = $this->getReadModel($event->getCustomerId());
        $readModel->setActive(true);
        $this->repository->save($readModel);
    }

    public function applyCustomerWasAssignedToTransaction(CustomerWasAssignedToTransaction $event)
    {
        $readModel = $this->getReadModel(new CustomerId($event->getCustomerId()->__toString()));
        $transaction = $this->transactionDetailsRepository->find($event->getTransactionId()->__toString());
        if (!$transaction instanceof TransactionDetails) {
            return;
        }
        $revisedTransaction = null;
        if ($transaction->getRevisedDocument() && $transaction->getDocumentType() == Transaction::TYPE_RETURN) {
            $tmp = $this->transactionDetailsRepository->findBy(['documentNumberRaw' => $transaction->getRevisedDocument()]);
            if (count($tmp) > 0) {
                $revisedTransaction = reset($tmp);
            }
        }
        if ($revisedTransaction instanceof TransactionDetails) {
            if ($revisedTransaction->getGrossValue() + $transaction->getGrossValue() <= 0) {
                $readModel->setTransactionsCount($readModel->getTransactionsCount() - 1);
            }
        } else {
            $readModel->setTransactionsCount($readModel->getTransactionsCount() + 1);
        }
        $readModel->setTransactionsAmount($readModel->getTransactionsAmount() + $transaction->getGrossValue());
        $readModel->setTransactionsAmountWithoutDeliveryCosts($readModel->getTransactionsAmountWithoutDeliveryCosts() + $transaction->getGrossValueWithoutDeliveryCosts());
        $readModel->addTransactionId(new TransactionId($event->getTransactionId()->__toString()));
        $readModel->setAverageTransactionAmount($readModel->getTransactionsCount() == 0 ? 0 : $readModel->getTransactionsAmount() / $readModel->getTransactionsCount());
        $readModel->setAmountExcludedForLevel($readModel->getAmountExcludedForLevel() + $transaction->getAmountExcludedForLevel());
        if ($transaction->getPurchaseDate() > $readModel->getLastTransactionDate()) {
            $readModel->setLastTransactionDate($transaction->getPurchaseDate());
        }

        $this->repository->save($readModel);
    }

    private function getReadModel(CustomerId $userId)
    {
        $readModel = $this->repository->find($userId->__toString());

        if (null === $readModel) {
            $readModel = new CustomerDetails($userId);
        }

        return $readModel;
    }
}
