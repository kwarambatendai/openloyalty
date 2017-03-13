<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Segment\SystemEvent;

/**
 * Class SegmentSystemEvents.
 */
class SegmentSystemEvents
{
    const CUSTOMER_ADDED_TO_SEGMENT = 'oloy.segment.customer_added_to_segment';
    const CUSTOMER_REMOVED_FROM_SEGMENT = 'oloy.segment.customer_removed_from_segment';
    const SEGMENT_CHANGED = 'oloy.segment.changed';
}
