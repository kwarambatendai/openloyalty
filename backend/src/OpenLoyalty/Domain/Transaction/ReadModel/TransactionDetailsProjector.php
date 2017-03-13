<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Transaction\ReadModel;

use Broadway\ReadModel\Projector;
use OpenLoyalty\Domain\Pos\Pos;
use OpenLoyalty\Domain\Pos\PosId;
use OpenLoyalty\Domain\Pos\PosRepository;
use OpenLoyalty\Domain\Transaction\Event\CustomerWasAssignedToTransaction;
use OpenLoyalty\Domain\Transaction\Event\TransactionWasRegistered;
use OpenLoyalty\Domain\Transaction\Model\CustomerBasicData;
use OpenLoyalty\Domain\Transaction\TransactionId;

/**
 * Class TransactionDetailsProjector.
 */
class TransactionDetailsProjector extends Projector
{
    private $repository;

    /**
     * @var PosRepository
     */
    private $posRepository;

    /**
     * TransactionDetailsProjector constructor.
     *
     * @param               $repository
     * @param PosRepository $posRepository
     */
    public function __construct($repository, PosRepository $posRepository)
    {
        $this->repository = $repository;
        $this->posRepository = $posRepository;
    }

    protected function applyTransactionWasRegistered(TransactionWasRegistered $event)
    {
        $readModel = $this->getReadModel($event->getTransactionId());
        $transactionData = $event->getTransactionData();
        $readModel->setDocumentType($transactionData['documentType']);
        $readModel->setDocumentNumber($transactionData['documentNumber']);
        $readModel->setPurchaseDate($transactionData['purchaseDate']);
        $readModel->setPurchasePlace($transactionData['purchasePlace']);
        $readModel->setCustomerData(CustomerBasicData::deserialize($event->getCustomerData()));
        $readModel->setItems($event->getItems());
        $readModel->setPosId($event->getPosId());
        $readModel->setExcludedDeliverySKUs($event->getExcludedDeliverySKUs());
        $readModel->setExcludedLevelSKUs($event->getExcludedLevelSKUs());
        $readModel->setExcludedLevelCategories($event->getExcludedLevelCategories());
        $readModel->setRevisedDocument($event->getRevisedDocument());

        if ($readModel->getPosId()) {
            /** @var Pos $pos */
            $pos = $this->posRepository->byId(new PosId($readModel->getPosId()->__toString()));
            if ($pos) {
                $pos->setTransactionsAmount($pos->getTransactionsAmount() + $readModel->getGrossValue());
                $pos->setTransactionsCount($pos->getTransactionsCount() + 1);
                $this->posRepository->save($pos);
            }
        }

        $this->repository->save($readModel);
    }

    public function applyCustomerWasAssignedToTransaction(CustomerWasAssignedToTransaction $event)
    {
        $readModel = $this->getReadModel($event->getTransactionId());
        $readModel->setCustomerId($event->getCustomerId());
        $this->repository->save($readModel);
    }

    /**
     * @param TransactionId $transactionId
     *
     * @return TransactionDetails|null
     */
    private function getReadModel(TransactionId $transactionId)
    {
        /** @var TransactionDetails $readModel */
        $readModel = $this->repository->find($transactionId->__toString());

        if (null === $readModel) {
            $readModel = new TransactionDetails($transactionId);
        }

        return $readModel;
    }
}
