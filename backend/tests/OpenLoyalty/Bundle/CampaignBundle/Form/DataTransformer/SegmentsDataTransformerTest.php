<?php

namespace OpenLoyalty\Bundle\CampaignBundle\Form\DataTransformer;

use OpenLoyalty\Bundle\CampaignBundle\Form\DataTransformer\SegmentsDataTransformer;
use OpenLoyalty\Domain\Campaign\SegmentId;

/**
 * Class SegmentsDataTransformerTest.
 */
class SegmentsDataTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_transforms()
    {
        $data = [
            new SegmentId('000096cf-32a3-43bd-9034-4df343e5fd93'),
            new SegmentId('000096cf-32a3-43bd-9034-4df343e5fd91'),
            new SegmentId('000096cf-32a3-43bd-9034-4df343e5fd90'),
        ];
        $transformer = new SegmentsDataTransformer();
        $transformed = $transformer->transform($data);
        $this->assertEquals([
            '000096cf-32a3-43bd-9034-4df343e5fd93',
            '000096cf-32a3-43bd-9034-4df343e5fd91',
            '000096cf-32a3-43bd-9034-4df343e5fd90',
        ], $transformed);
    }

    /**
     * @test
     */
    public function it_reverse_transforms()
    {
        $data = [
            '000096cf-32a3-43bd-9034-4df343e5fd93',
            '000096cf-32a3-43bd-9034-4df343e5fd91',
            '000096cf-32a3-43bd-9034-4df343e5fd90',
        ];
        $transformer = new SegmentsDataTransformer();
        $transformed = $transformer->reverseTransform($data);
        $this->assertEquals([
            new SegmentId('000096cf-32a3-43bd-9034-4df343e5fd93'),
            new SegmentId('000096cf-32a3-43bd-9034-4df343e5fd91'),
            new SegmentId('000096cf-32a3-43bd-9034-4df343e5fd90'),
        ], $transformed);
    }
}
