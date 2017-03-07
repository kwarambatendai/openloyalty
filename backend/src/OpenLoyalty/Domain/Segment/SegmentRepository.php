<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Segment;

interface SegmentRepository
{
    public function byId(SegmentId $segmentId);

    public function findAll();

    public function findAllActive();

    public function save(Segment $segment);

    public function remove(Segment $segment);

    public function findAllPaginated($page = 1, $perPage = 10, $sortField = 'segmentId', $direction = 'DESC', $onlyActive = false);

    public function countTotal();
}
