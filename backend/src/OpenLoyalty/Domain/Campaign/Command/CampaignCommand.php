<?php

namespace OpenLoyalty\Domain\Campaign\Command;

use OpenLoyalty\Domain\Campaign\CampaignId;

/**
 * Class CampaignCommand.
 */
abstract class CampaignCommand
{
    /**
     * @var CampaignId
     */
    protected $campaignId;

    /**
     * CampaignCommand constructor.
     *
     * @param CampaignId $campaignId
     */
    public function __construct(CampaignId $campaignId)
    {
        $this->campaignId = $campaignId;
    }

    /**
     * @return CampaignId
     */
    public function getCampaignId()
    {
        return $this->campaignId;
    }
}
