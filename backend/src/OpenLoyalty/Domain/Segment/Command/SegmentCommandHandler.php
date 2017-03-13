<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Segment\Command;

use Broadway\CommandHandling\CommandHandler;
use Broadway\EventDispatcher\EventDispatcherInterface;
use OpenLoyalty\Domain\Segment\Model\Criterion;
use OpenLoyalty\Domain\Segment\Model\SegmentPart;
use OpenLoyalty\Domain\Segment\Segment;
use OpenLoyalty\Domain\Segment\SegmentPartId;
use OpenLoyalty\Domain\Segment\SegmentPartRepository;
use OpenLoyalty\Domain\Segment\SegmentRepository;
use OpenLoyalty\Domain\Segment\SystemEvent\SegmentChangedSystemEvent;
use OpenLoyalty\Domain\Segment\SystemEvent\SegmentSystemEvents;

/**
 * Class SegmentCommandHandler.
 */
class SegmentCommandHandler extends CommandHandler
{
    /**
     * @var SegmentRepository
     */
    protected $segmentRepository;

    /**
     * @var SegmentPartRepository
     */
    protected $segmentPartRepository;
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * SegmentCommandHandler constructor.
     *
     * @param SegmentRepository        $segmentRepository
     * @param SegmentPartRepository    $segmentPartRepository
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        SegmentRepository $segmentRepository,
        SegmentPartRepository $segmentPartRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->segmentRepository = $segmentRepository;
        $this->segmentPartRepository = $segmentPartRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function handleCreateSegment(CreateSegment $command)
    {
        $data = $command->getSegmentData();
        $segment = new Segment($command->getSegmentId(), $data['name'], $data['description']);

        $partsData = $data['parts'];
        foreach ($partsData as $part) {
            $newPart = new SegmentPart(new SegmentPartId($part['segmentPartId']));
            $criteriaData = $part['criteria'];
            foreach ($criteriaData as $criterion) {
                $class = Criterion::TYPE_MAP[$criterion['type']];

                $criterion = $class::fromArray($criterion);
                $newPart->addCriterion($criterion);
            }
            $segment->addPart($newPart);
        }

        $this->segmentRepository->save($segment);
    }

    public function handleUpdateSegment(UpdateSegment $command)
    {
        $data = $command->getSegmentData();
        /** @var Segment $segment */
        $segment = $this->segmentRepository->byId($command->getSegmentId());
        if (isset($data['name'])) {
            $segment->setName($data['name']);
        }
        if (isset($data['description'])) {
            $segment->setDescription($data['description']);
        }
        if (isset($data['parts'])) {
            foreach ($segment->getParts() as $part) {
                $segment->removePart($part);
                $this->segmentPartRepository->remove($part);
            }
            $partsData = $data['parts'];
            foreach ($partsData as $part) {
                $newPart = new SegmentPart(new SegmentPartId($part['segmentPartId']));
                $criteriaData = $part['criteria'];
                foreach ($criteriaData as $criterion) {
                    $class = Criterion::TYPE_MAP[$criterion['type']];

                    $criterion = $class::fromArray($criterion);
                    $newPart->addCriterion($criterion);
                }
                $segment->addPart($newPart);
            }
        }

        $this->segmentRepository->save($segment);
        if ($this->eventDispatcher && $segment->isActive() === true) {
            $this->eventDispatcher->dispatch(
                SegmentSystemEvents::SEGMENT_CHANGED,
                [
                    new SegmentChangedSystemEvent($segment->getSegmentId(), $segment->getName()),
                ]
            );
        }
    }

    public function handleActivateSegment(ActivateSegment $command)
    {
        /** @var Segment $segment */
        $segment = $this->segmentRepository->byId($command->getSegmentId());
        $segment->setActive(true);
        $this->segmentRepository->save($segment);
        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch(
                SegmentSystemEvents::SEGMENT_CHANGED,
                [
                    new SegmentChangedSystemEvent($segment->getSegmentId(), $segment->getName()),
                ]
            );
        }
    }

    public function handleDeactivateSegment(DeactivateSegment $command)
    {
        /** @var Segment $segment */
        $segment = $this->segmentRepository->byId($command->getSegmentId());
        $segment->setActive(false);
        $this->segmentRepository->save($segment);
    }

    public function handleDeleteSegment(DeleteSegment $command)
    {
        /** @var Segment $segment */
        $segment = $this->segmentRepository->byId($command->getSegmentId());
        $this->segmentRepository->remove($segment);
    }
}
