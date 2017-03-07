<?php

namespace OpenLoyalty\Domain\Segment;

use OpenLoyalty\Domain\Segment\Model\SegmentPart;

interface SegmentPartRepository
{
    public function byId(SegmentPartId $segmentPartId);

    public function findAll();

    public function save(SegmentPart $segmentPart);

    public function remove(SegmentPart $segmentPart);
}
