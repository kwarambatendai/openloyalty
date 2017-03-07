<?php

namespace OpenLoyalty\Domain\Segment\Command;

use OpenLoyalty\Domain\Segment\Model\Criterion;
use OpenLoyalty\Domain\Segment\Segment;
use OpenLoyalty\Domain\Segment\SegmentId;

/**
 * Class EditSegmentTest.
 */
class EditSegmentTest extends SegmentCommandHandlerTest
{
    /**
     * @test
     */
    public function it_updates_segment()
    {
        $handler = $this->createCommandHandler();
        $segmentId = new SegmentId('00000000-0000-0000-0000-000000001111');
        $posId = '00000000-0000-0000-0000-000000000000';

        $command = new UpdateSegment($segmentId, [
            'name' => 'test-updated',
            'parts' => [
                [
                    'segmentPartId' => '00000000-0000-0000-0000-000000000000',
                    'criteria' => [
                        [
                            'type' => Criterion::TYPE_BOUGHT_IN_POS,
                            'criterionId' => '00000000-0000-0000-0000-000000000000',
                            'posIds' => [$posId],
                        ],
                    ]
                ],
            ],
        ]);
        $handler->handle($command);
        /** @var Segment $segment */
        $segment = $this->inMemoryRepository->byId($segmentId);
        $this->assertNotNull($segment);
        $this->assertInstanceOf(Segment::class, $segment);
        $this->assertTrue(count($segment->getParts()) == 1);
        $this->assertEquals('test-updated', $segment->getName());
        $parts = $segment->getParts();
        $part = reset($parts);
        $this->assertTrue(count($part->getCriteria()) == 1);
    }
}
