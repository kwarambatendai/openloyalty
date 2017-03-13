<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Campaign\ReadModel;

use Broadway\ReadModel\RepositoryInterface;
use OpenLoyalty\Domain\Campaign\CampaignId;
use OpenLoyalty\Domain\Campaign\CustomerId;

interface CouponUsageRepository extends RepositoryInterface
{
    public function countUsageForCampaign(CampaignId $campaignId);

    public function countUsageForCampaignAndCustomer(CampaignId $campaignId, CustomerId $customerId);

    public function findByCampaign(CampaignId $campaignId);
}
