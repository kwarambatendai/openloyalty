<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Campaign;

use OpenLoyalty\Domain\Identifier;
use Assert\Assertion as Assert;

/**
 * Class SegmentId.
 */
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
