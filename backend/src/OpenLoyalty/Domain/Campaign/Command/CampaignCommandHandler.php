<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Campaign\Command;

use Broadway\CommandHandling\CommandHandler;
use OpenLoyalty\Domain\Campaign\Campaign;
use OpenLoyalty\Domain\Campaign\CampaignRepository;

/**
 * Class CampaignCommandHandler.
 */
class CampaignCommandHandler extends CommandHandler
{
    /**
     * @var CampaignRepository
     */
    protected $campaignRepository;

    /**
     * CampaignCommandHandler constructor.
     *
     * @param CampaignRepository $campaignRepository
     */
    public function __construct(CampaignRepository $campaignRepository)
    {
        $this->campaignRepository = $campaignRepository;
    }

    public function handleCreateCampaign(CreateCampaign $command)
    {
        $data = $command->getCampaignData();
        Campaign::validateRequiredData($data);
        $campaign = new Campaign($command->getCampaignId(), $data);
        $this->campaignRepository->save($campaign);
    }

    public function handleUpdateCampaign(UpdateCampaign $command)
    {
        $data = $command->getCampaignData();
        Campaign::validateRequiredData($data);
        /** @var Campaign $campaign */
        $campaign = $this->campaignRepository->byId($command->getCampaignId());
        $campaign->setFromArray($command->getCampaignData());

        $this->campaignRepository->save($campaign);
    }

    public function handleChangeCampaignState(ChangeCampaignState $command)
    {
        /** @var Campaign $campaign */
        $campaign = $this->campaignRepository->byId($command->getCampaignId());
        $campaign->setActive($command->getActive());

        $this->campaignRepository->save($campaign);
    }

    public function handleSetCampaignPhoto(SetCampaignPhoto $command)
    {
        /** @var Campaign $campaign */
        $campaign = $this->campaignRepository->byId($command->getCampaignId());
        $campaign->setCampaignPhoto($command->getCampaignPhoto());

        $this->campaignRepository->save($campaign);
    }

    public function handleRemoveCampaignPhoto(RemoveCampaignPhoto $command)
    {
        /** @var Campaign $campaign */
        $campaign = $this->campaignRepository->byId($command->getCampaignId());
        $campaign->setCampaignPhoto(null);

        $this->campaignRepository->save($campaign);
    }
}
