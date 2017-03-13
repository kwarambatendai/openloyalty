<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Campaign\Command;

use OpenLoyalty\Domain\Campaign\CampaignId;

/**
 * Class ChangeCampaignState.
 */
class ChangeCampaignState extends CampaignCommand
{
    protected $active;

    public function __construct(CampaignId $campaignId, $active)
    {
        parent::__construct($campaignId);
        $this->active = $active;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }
}
