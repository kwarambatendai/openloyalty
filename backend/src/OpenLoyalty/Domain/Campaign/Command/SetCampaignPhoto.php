<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Campaign\Command;

use OpenLoyalty\Domain\Campaign\CampaignId;
use OpenLoyalty\Domain\Campaign\Model\CampaignPhoto;

/**
 * Class SetCampaignPhoto.
 */
class SetCampaignPhoto extends CampaignCommand
{
    /**
     * @var CampaignPhoto
     */
    protected $campaignPhoto;

    public function __construct(CampaignId $campaignId, CampaignPhoto $campaignPhoto = null)
    {
        parent::__construct($campaignId);
        $this->campaignPhoto = $campaignPhoto;
    }

    /**
     * @return CampaignPhoto
     */
    public function getCampaignPhoto()
    {
        return $this->campaignPhoto;
    }
}
