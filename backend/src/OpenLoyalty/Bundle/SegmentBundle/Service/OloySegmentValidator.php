<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\SegmentBundle\Service;

use OpenLoyalty\Domain\Segment\SegmentRepository;

/**
 * Class OloyCustomerValidator.
 */
class OloySegmentValidator
{
    /**
     * @var SegmentRepository
     */
    protected $segmentRepository;

    /**
     * OloyCustomerValidator constructor.
     *
     * @param SegmentRepository $segmentRepository
     */
    public function __construct(SegmentRepository $segmentRepository)
    {
        $this->segmentRepository = $segmentRepository;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function exists($name)
    {
        $segment = $this->segmentRepository->findBy(['name' => $name]);
        if ($segment) {
            return true;
        }
    }

    /**
     * @param $name
     * @param $id
     *
     * @return bool
     */
    public function updateExists($name, $id)
    {
        $segment = $this->segmentRepository->findOneBy(['name' => $name]);
        if ($segment && $id != $segment->getSegmentId()->__toString()) {
            return true;
        }
    }
}
