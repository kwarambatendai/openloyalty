<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Segment;

use OpenLoyalty\Domain\Segment\Model\SegmentPart;

/**
 * Class Segment.
 */
class Segment
{
    /**
     * @var SegmentId
     */
    protected $segmentId;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var bool
     */
    protected $active = false;

    /**
     * @var SegmentPart[]
     */
    protected $parts;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var int
     */
    protected $customersCount = 0;

    public function __construct(SegmentId $segmentId, $name, $description = null)
    {
        $this->segmentId = $segmentId;
        $this->name = $name;
        $this->description = $description;
        $this->parts = [];
        $this->createdAt = new \DateTime();
    }

    /**
     * @return SegmentId
     */
    public function getSegmentId()
    {
        return $this->segmentId;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return Model\SegmentPart[]
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * @param Model\SegmentPart[] $parts
     */
    public function setParts($parts)
    {
        $this->parts = $parts;
    }

    public function addPart(SegmentPart $part)
    {
        $part->setSegment($this);
        $this->parts[$part->getSegmentPartId()->__toString()] = $part;
    }

    public function removePart(SegmentPart $part)
    {
        $part->setSegment(null);
        unset($this->parts[$part->getSegmentPartId()->__toString()]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getCustomersCount()
    {
        return $this->customersCount;
    }

    /**
     * @param int $customersCount
     */
    public function setCustomersCount($customersCount)
    {
        $this->customersCount = $customersCount;
    }
}
