<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Segment\Model;

use OpenLoyalty\Domain\Segment\Segment;
use OpenLoyalty\Domain\Segment\SegmentPartId;
use Assert\Assertion as Assert;

/**
 * Class SegmentPart.
 */
class SegmentPart
{
    /**
     * @var SegmentPartId
     */
    protected $segmentPartId;

    /**
     * @var Segment
     */
    protected $segment;

    /**
     * @var Criterion[]
     */
    protected $criteria;

    /**
     * SegmentPart constructor.
     *
     * @param SegmentPartId $segmentPartId
     */
    public function __construct(SegmentPartId $segmentPartId)
    {
        $this->segmentPartId = $segmentPartId;
        $this->criteria = [];
    }

    /**
     * @return SegmentPartId
     */
    public function getSegmentPartId()
    {
        return $this->segmentPartId;
    }

    /**
     * @return Segment
     */
    public function getSegment()
    {
        return $this->segment;
    }

    /**
     * @param Segment $segment
     */
    public function setSegment($segment)
    {
        $this->segment = $segment;
    }

    /**
     * @return Criterion[]
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @param Criterion[] $criteria
     */
    public function setCriteria($criteria)
    {
        $this->criteria = $criteria;
    }

    public function addCriterion(Criterion $criterion)
    {
        $criterion->setSegmentPart($this);
        $this->criteria[$criterion->getCriterionId()->__toString()] = $criterion;
    }

    public function removeCriterion(Criterion $criterion)
    {
        $criterion->setSegmentPart(null);
        unset($this->criteria[$criterion->getCriterionId()->__toString()]);
    }

    public static function validate(array $data)
    {
        Assert::keyIsset($data, 'segmentPartId');
        Assert::notBlank($data['segmentPartId']);
        Assert::keyIsset($data, 'criteria');
        Assert::greaterOrEqualThan(count($data['criteria']), 1);
    }
}
