<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Segment\SystemEvent;

use OpenLoyalty\Domain\Segment\SegmentId;

/**
 * Class SegmentSystemEvent.
 */
class SegmentSystemEvent
{
    /**
     * @var SegmentId
     */
    protected $segmentId;

    /**
     * SegmentSystemEvent constructor.
     *
     * @param SegmentId $segmentId
     */
    public function __construct(SegmentId $segmentId)
    {
        $this->segmentId = $segmentId;
    }

    /**
     * @return SegmentId
     */
    public function getSegmentId()
    {
        return $this->segmentId;
    }
}
