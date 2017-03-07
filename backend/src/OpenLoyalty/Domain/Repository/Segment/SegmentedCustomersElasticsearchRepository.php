<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Repository\Segment;

use OpenLoyalty\Domain\Repository\OloyElasticsearchRepository;
use OpenLoyalty\Domain\Segment\ReadModel\SegmentedCustomersRepository;

/**
 * Class SegmentedCustomersElasticsearchRepository.
 */
class SegmentedCustomersElasticsearchRepository extends OloyElasticsearchRepository implements SegmentedCustomersRepository
{
}
