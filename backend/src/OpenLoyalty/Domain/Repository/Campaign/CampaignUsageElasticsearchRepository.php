<?php

namespace OpenLoyalty\Domain\Repository\Campaign;

use OpenLoyalty\Domain\Campaign\ReadModel\CampaignUsageRepository;
use OpenLoyalty\Domain\Repository\OloyElasticsearchRepository;

/**
 * Class SegmentedCustomersElasticsearchRepository.
 */
class CampaignUsageElasticsearchRepository extends OloyElasticsearchRepository implements CampaignUsageRepository
{
}
