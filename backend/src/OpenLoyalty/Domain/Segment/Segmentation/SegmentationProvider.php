<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Segment\Segmentation;

use OpenLoyalty\Domain\Segment\Model\Criterion;
use OpenLoyalty\Domain\Segment\Model\SegmentPart;
use OpenLoyalty\Domain\Segment\Segment;
use OpenLoyalty\Domain\Segment\Segmentation\CriteriaEvaluators\Evaluator;
use Psr\Log\LoggerInterface;

/**
 * Class SegmentationProvider.
 */
class SegmentationProvider
{
    /**
     * @var Evaluator[]
     */
    protected $evaluators;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    protected $maxExecutionTimeBeforeWarning = 3; // in minutes

    public function evaluateSegment(Segment $segment)
    {
        $customers = null;

        /** @var SegmentPart $part */
        foreach ($segment->getParts() as $part) {
            if (!$customers) {
                $customers = $this->getCustomersForPart($part);
            } else {
                $customers = array_intersect($customers, $this->getCustomersForPart($part));
            }
        }

        return $customers;
    }

    public function addEvaluator(Evaluator $evaluator)
    {
        $this->evaluators[] = $evaluator;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param int $maxExecutionTimeBeforeWarning
     */
    public function setMaxExecutionTimeBeforeWarning($maxExecutionTimeBeforeWarning)
    {
        $this->maxExecutionTimeBeforeWarning = $maxExecutionTimeBeforeWarning;
    }

    protected function getCustomersForPart(SegmentPart $part)
    {
        $customers = [];

        /** @var Criterion $criterion */
        foreach ($part->getCriteria() as $criterion) {
            foreach ($this->evaluators as $evaluator) {
                if (!$evaluator->support($criterion)) {
                    continue;
                }
                $timeStart = microtime(true);
                $customers = array_merge($customers, $evaluator->evaluate($criterion));
                $timeEnd = microtime(true);
                $executionTimeInMinutes = ($timeEnd - $timeStart) / 60;
                if ($this->logger && $executionTimeInMinutes >= $this->maxExecutionTimeBeforeWarning) {
                    $refl = new \ReflectionClass($evaluator);
                    $this->logger->alert($refl->getShortName().' needs too much time', [
                        'evaluator' => $refl->getShortName(),
                        'evaluator_class' => $refl->getName(),
                        'segment' => $part->getSegment()->getSegmentId()->__toString(),
                        'segment_part' => $part->getSegmentPartId()->__toString(),
                        'criterion' => $criterion->getCriterionId()->__toString(),
                        'execution_time_in_minutes' => $executionTimeInMinutes,
                    ]);
                }
            }
        }

        return $customers;
    }
}
