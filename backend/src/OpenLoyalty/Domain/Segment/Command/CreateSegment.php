<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Segment\Command;

use OpenLoyalty\Domain\Segment\Model\Criterion;
use OpenLoyalty\Domain\Segment\Model\SegmentPart;
use OpenLoyalty\Domain\Segment\SegmentId;
use Assert\Assertion as Assert;

/**
 * Class CreateSegment.
 */
class CreateSegment extends SegmentCommand
{
    /**
     * @var array
     */
    protected $segmentData;

    public function __construct(SegmentId $segmentId, array $segmentData)
    {
        $this->validate($segmentData);
        parent::__construct($segmentId);
        $this->segmentData = $segmentData;
    }

    protected function validate(array $segmentData)
    {
        Assert::keyIsset($segmentData, 'name');
        Assert::notBlank($segmentData['name']);
        Assert::keyIsset($segmentData, 'parts');
        Assert::notBlank($segmentData['parts']);
        Assert::greaterOrEqualThan(count($segmentData['parts']), 1);
        foreach ($segmentData['parts'] as $part) {
            SegmentPart::validate($part);
            foreach ($part['criteria'] as $criterion) {
                $map = Criterion::TYPE_MAP;
                if (!isset($map[$criterion['type']])) {
                    throw new \Exception('type '.$criterion['type'].' does not exists');
                }
                $map[$criterion['type']]::validate($criterion);
            }
        }
    }

    /**
     * @return array
     */
    public function getSegmentData()
    {
        return $this->segmentData;
    }
}
