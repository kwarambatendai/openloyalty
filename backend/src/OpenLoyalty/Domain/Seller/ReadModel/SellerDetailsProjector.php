<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Seller\ReadModel;

use Broadway\ReadModel\Projector;
use OpenLoyalty\Domain\Pos\Pos;
use OpenLoyalty\Domain\Pos\PosRepository;
use OpenLoyalty\Domain\Seller\Event\SellerWasActivated;
use OpenLoyalty\Domain\Seller\Event\SellerWasDeactivated;
use OpenLoyalty\Domain\Seller\Event\SellerWasDeleted;
use OpenLoyalty\Domain\Seller\Event\SellerWasRegistered;
use OpenLoyalty\Domain\Seller\Event\SellerWasUpdated;
use OpenLoyalty\Domain\Seller\PosId;
use OpenLoyalty\Domain\Seller\SellerId;

/**
 * Class SellerDetailsProjector.
 */
class SellerDetailsProjector extends Projector
{
    private $repository;

    /**
     * @var PosRepository
     */
    protected $posRepository;

    /**
     * SellerDetailsProjector constructor.
     *
     * @param               $repository
     * @param PosRepository $posRepository
     */
    public function __construct($repository, PosRepository $posRepository)
    {
        $this->repository = $repository;
        $this->posRepository = $posRepository;
    }

    protected function applySellerWasRegistered(SellerWasRegistered $event)
    {
        $data = $event->getSellerData();
        if (isset($data['posId']) && !$data['posId'] instanceof PosId) {
            $data['posId'] = new PosId($data['posId']);
        }
        $readModel = $this->getReadModel($event->getSellerId());
        $readModel->setFirstName($data['firstName']);
        $readModel->setLastName($data['lastName']);
        $readModel->setEmail($data['email']);
        $readModel->setPhone($data['phone']);
        if (isset($data['posId'])) {
            $readModel->setPosId($data['posId']);
        }
        if ($readModel->getPosId()) {
            /** @var Pos $pos */
            $pos = $this->posRepository->byId(new \OpenLoyalty\Domain\Pos\PosId($readModel->getPosId()->__toString()));
            if ($pos) {
                $readModel->setPosName($pos->getName());
                $readModel->setPosCity($pos->getLocation() ? $pos->getLocation()->getCity() : null);
            }
        }

        if (isset($data['active'])) {
            $readModel->setActive($data['active']);
        }
        if (isset($data['deleted'])) {
            $readModel->setDeleted($data['deleted']);
        }
        $this->repository->save($readModel);
    }

    protected function applySellerWasDeleted(SellerWasDeleted $event)
    {
        $readModel = $this->getReadModel($event->getSellerId());
        $readModel->setDeleted(true);
        $this->repository->save($readModel);
    }

    protected function applySellerWasActivated(SellerWasActivated $event)
    {
        $readModel = $this->getReadModel($event->getSellerId());
        $readModel->setActive(true);
        $this->repository->save($readModel);
    }

    protected function applySellerWasDeactivated(SellerWasDeactivated $event)
    {
        $readModel = $this->getReadModel($event->getSellerId());
        $readModel->setActive(false);
        $this->repository->save($readModel);
    }

    protected function applySellerWasUpdated(SellerWasUpdated $event)
    {
        $readModel = $this->getReadModel($event->getSellerId());
        $data = $event->getSellerData();

        if (isset($data['firstName'])) {
            $readModel->setFirstName($data['firstName']);
        }

        if (isset($data['lastName'])) {
            $readModel->setLastName($data['lastName']);
        }

        if (isset($data['email'])) {
            $readModel->setEmail($data['email']);
        }

        if (isset($data['phone'])) {
            $readModel->setPhone($data['phone']);
        }

        if (isset($data['posId'])) {
            if (!$data['posId'] instanceof PosId) {
                $data['posId'] = new PosId($data['posId']);
            }
            $readModel->setPosId($data['posId']);
            if ($readModel->getPosId()) {
                /** @var Pos $pos */
                $pos = $this->posRepository->byId(new \OpenLoyalty\Domain\Pos\PosId($readModel->getPosId()->__toString()));
                if ($pos) {
                    $readModel->setPosName($pos->getName());
                    $readModel->setPosCity($pos->getLocation() ? $pos->getLocation()->getCity() : null);
                }
            }
        }

        $this->repository->save($readModel);
    }

    private function getReadModel(SellerId $sellerId)
    {
        $readModel = $this->repository->find($sellerId->__toString());

        if (null === $readModel) {
            $readModel = new SellerDetails($sellerId);
        }

        return $readModel;
    }
}
