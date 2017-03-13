<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Campaign\Command;

use OpenLoyalty\Domain\Campaign\CampaignId;

/**
 * Class CreateCampaign.
 */
class CreateCampaign extends CampaignCommand
{
    /**
     * @var array
     */
    protected $campaignData;

    public function __construct(CampaignId $campaignId, array $campaignData)
    {
        parent::__construct($campaignId);
        $this->campaignData = $campaignData;
    }

    /**
     * @return array
     */
    public function getCampaignData()
    {
        return $this->campaignData;
    }
}
