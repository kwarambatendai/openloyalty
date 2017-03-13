<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Segment\SystemEvent;

use OpenLoyalty\Domain\Segment\SegmentId;

/**
 * Class SegmentChangedSystemEvent.
 */
class SegmentChangedSystemEvent extends SegmentSystemEvent
{
    /**
     * @var string
     */
    protected $segmentName;
    /**
     * SegmentSystemEvent constructor.
     *
     * @param SegmentId $segmentId
     * @param string    $segmentName
     */
    public function __construct(SegmentId $segmentId, $segmentName)
    {
        parent::__construct($segmentId);
        $this->segmentName = $segmentName;
    }

    /**
     * @return string
     */
    public function getSegmentName()
    {
        return $this->segmentName;
    }
}
