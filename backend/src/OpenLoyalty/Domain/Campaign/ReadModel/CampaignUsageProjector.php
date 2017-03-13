<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Campaign\ReadModel;

use Broadway\Domain\DomainMessage;
use Broadway\EventDispatcher\EventDispatcherInterface;
use Broadway\EventHandling\EventListenerInterface;
use Broadway\ReadModel\RepositoryInterface;
use OpenLoyalty\Domain\Campaign\CampaignId;
use OpenLoyalty\Domain\Customer\Event\CampaignWasBoughtByCustomer;
use Psr\Log\LoggerInterface;

/**
 * Class CampaignUsageProjector.
 */
class CampaignUsageProjector implements EventListenerInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * CampaignUsageProjector constructor.
     *
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param DomainMessage $domainMessage
     */
    public function handle(DomainMessage $domainMessage)
    {
        $event = $domainMessage->getPayload();
        if ($event instanceof CampaignWasBoughtByCustomer) {
            $this->storeCampaignUsages(new CampaignId($event->getCampaignId()->__toString()));
        }
    }

    /**
     * @param CampaignId $campaignId
     */
    public function storeCampaignUsages(CampaignId $campaignId)
    {
        $readModel = $this->getReadModel($campaignId);
        if ($readModel->getCampaignUsage() !== null) {
            $readModel->setCampaignUsage($readModel->getCampaignUsage() + 1);
        } else {
            $readModel->setCampaignUsage(1);
        }
        $this->repository->save($readModel);
    }

    /**
     * @param CampaignId $campaignId
     *
     * @return \Broadway\ReadModel\ReadModelInterface|null|CampaignUsage
     */
    private function getReadModel(CampaignId $campaignId)
    {
        $readModel = $this->repository->find($campaignId);
        if (null === $readModel) {
            $readModel = new CampaignUsage($campaignId);
        }

        return $readModel;
    }
}
