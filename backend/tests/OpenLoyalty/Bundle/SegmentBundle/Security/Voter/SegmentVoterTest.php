<?php

namespace OpenLoyalty\Bundle\SegmentBundle\Security\Voter;

use OpenLoyalty\Bundle\BaseVoterTest;
use OpenLoyalty\Domain\Campaign\SegmentId;
use OpenLoyalty\Domain\Segment\Segment;

/**
 * Class SegmentVoterTest.
 */
class SegmentVoterTest extends BaseVoterTest
{
    const SEGMENT_ID = '00000000-0000-474c-b092-b0dd880c0700';

    /**
     * @test
     */
    public function it_works()
    {
        $attributes = [
            SegmentVoter::LIST_SEGMENTS => ['seller' => false, 'customer' => false, 'admin' => true],
            SegmentVoter::CREATE_SEGMENT => ['seller' => false, 'customer' => false, 'admin' => true],
            SegmentVoter::LIST_CUSTOMERS => ['seller' => false, 'customer' => false, 'admin' => true, 'id' => self::SEGMENT_ID],
            SegmentVoter::EDIT => ['seller' => false, 'customer' => false, 'admin' => true, 'id' => self::SEGMENT_ID],
            SegmentVoter::VIEW => ['seller' => false, 'customer' => false, 'admin' => true, 'id' => self::SEGMENT_ID],
            SegmentVoter::ACTIVATE => ['seller' => false, 'customer' => false, 'admin' => true, 'id' => self::SEGMENT_ID],
            SegmentVoter::DELETE => ['seller' => false, 'customer' => false, 'admin' => true, 'id' => self::SEGMENT_ID],
            SegmentVoter::DEACTIVATE => ['seller' => false, 'customer' => false, 'admin' => true, 'id' => self::SEGMENT_ID],
        ];

        $voter = new SegmentVoter();

        $this->makeAssertions($attributes, $voter);
    }

    protected function getSubjectById($id)
    {
        $segment = $this->getMockBuilder(Segment::class)->disableOriginalConstructor()->getMock();
        $segment->method('getSegmentId')->willReturn(new SegmentId($id));

        return $segment;
    }
}
