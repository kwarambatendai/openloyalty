<?php

namespace OpenLoyalty\Domain\Segment\Command;

use OpenLoyalty\Domain\Segment\Segment;
use OpenLoyalty\Domain\Segment\SegmentId;

/**
 * Class SegmentCommandHandlerTest.
 */
abstract class SegmentCommandHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected $inMemoryRepository;
    protected $partsInMemoryRepository;
    protected $eventDispatcher;

    protected $parts = [];

    protected $segment = [];

    public function setUp()
    {
        $segment = new Segment(new SegmentId('00000000-0000-0000-0000-000000001111'), 'test');
        $this->segment[] = $segment;

        $segments = &$this->segment;
        $this->inMemoryRepository = $this->getMock('OpenLoyalty\Domain\Segment\SegmentRepository');
        $this->partsInMemoryRepository = $this->getMock('OpenLoyalty\Domain\Segment\SegmentPartRepository');
        $this->eventDispatcher = $this->getMock('Broadway\EventDispatcher\EventDispatcher');
        $this->partsInMemoryRepository->method('remove')->with($this->any())->willReturn(true);
        $this->inMemoryRepository->method('save')->with($this->isInstanceOf(Segment::class))->will(
            $this->returnCallback(function($segment) use (&$segments) {
                $segments[] = $segment;

                return $segment;
            })
        );
        $this->inMemoryRepository->method('findAll')->with()->will(
            $this->returnCallback(function() use (&$segments) {
                return $segments;
            })
        );
        $this->inMemoryRepository->method('byId')->with($this->isInstanceOf(SegmentId::class))->will(
            $this->returnCallback(function($id) use (&$segments) {
                /** @var Segment $segment */
                foreach ($segments as $segment) {
                    if ($segment->getSegmentId()->__toString() == $id->__toString()) {
                        return $segment;
                    }
                }

                return null;
            })
        );
    }

    protected function createCommandHandler()
    {
        return new SegmentCommandHandler(
            $this->inMemoryRepository,
            $this->partsInMemoryRepository,
            $this->eventDispatcher
        );
    }
}
