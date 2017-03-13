<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Repository\Campaign;

use OpenLoyalty\Domain\Campaign\ReadModel\CampaignUsageRepository;
use OpenLoyalty\Domain\Repository\OloyElasticsearchRepository;

/**
 * Class SegmentedCustomersElasticsearchRepository.
 */
class CampaignUsageElasticsearchRepository extends OloyElasticsearchRepository implements CampaignUsageRepository
{
}
