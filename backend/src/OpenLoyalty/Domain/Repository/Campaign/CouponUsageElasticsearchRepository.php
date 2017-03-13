<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Repository\Campaign;

use OpenLoyalty\Domain\Campaign\CampaignId;
use OpenLoyalty\Domain\Campaign\CustomerId;
use OpenLoyalty\Domain\Campaign\ReadModel\CouponUsageRepository;
use OpenLoyalty\Domain\Repository\OloyElasticsearchRepository;

/**
 * Class CouponUsageElasticsearchRepository.
 */
class CouponUsageElasticsearchRepository extends OloyElasticsearchRepository implements CouponUsageRepository
{
    public function countUsageForCampaign(CampaignId $campaignId)
    {
        return $this->countTotal(['campaignId' => $campaignId->__toString()]);
    }

    public function countUsageForCampaignAndCustomer(CampaignId $campaignId, CustomerId $customerId)
    {
        return $this->countTotal([
            'campaignId' => $campaignId->__toString(),
            'customerId' => $customerId->__toString(),
        ]);
    }

    public function findByCampaign(CampaignId $campaignId)
    {
        return $this->findBy(['campaignId' => $campaignId->__toString()]);
    }
}
