<?php

namespace OpenLoyalty\Domain\Segment;

use Assert\Assertion as Assert;
use OpenLoyalty\Domain\Identifier;

class SegmentId implements Identifier
{
    /**
     * @var string
     */
    protected $segmentId;

    /**
     * SegmentId constructor.
     *
     * @param string $segmentId
     */
    public function __construct($segmentId)
    {
        Assert::string($segmentId);
        Assert::uuid($segmentId);

        $this->segmentId = $segmentId;
    }

    public function __toString()
    {
        return $this->segmentId;
    }
}
