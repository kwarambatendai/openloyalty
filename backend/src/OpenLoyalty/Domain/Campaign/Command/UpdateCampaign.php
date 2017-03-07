<?php

namespace OpenLoyalty\Domain\Campaign\Command;

use OpenLoyalty\Domain\Campaign\CampaignId;

/**
 * Class UpdateCampaign.
 */
class UpdateCampaign extends CampaignCommand
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
