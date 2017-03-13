<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Segment;

use Assert\Assertion as Assert;
use OpenLoyalty\Domain\Identifier;

/**
 * Class SegmentPartId.
 */
class SegmentPartId implements Identifier
{
    /**
     * @var string
     */
    protected $segmentPartId;

    /**
     * SegmentPartId constructor.
     *
     * @param string $segmentPartId
     */
    public function __construct($segmentPartId)
    {
        Assert::string($segmentPartId);
        Assert::uuid($segmentPartId);

        $this->segmentPartId = $segmentPartId;
    }

    public function __toString()
    {
        return $this->segmentPartId;
    }
}
