<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Segment\SystemEvent;

use OpenLoyalty\Domain\Segment\CustomerId;
use OpenLoyalty\Domain\Segment\SegmentId;

/**
 * Class CustomerRemovedFromSegmentSystemEvent.
 */
class CustomerRemovedFromSegmentSystemEvent extends SegmentSystemEvent
{
    /**
     * @var CustomerId
     */
    protected $customerId;

    public function __construct(SegmentId $segmentId, CustomerId $customerId)
    {
        parent::__construct($segmentId);
        $this->customerId = $customerId;
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }
}
