<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Segment\Segmentation\CriteriaEvaluators;

use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use OpenLoyalty\Domain\Segment\Model\Criteria\Anniversary;
use OpenLoyalty\Domain\Segment\Model\Criterion;

/**
 * Class AnniversaryEvaluator.
 */
class AnniversaryEvaluator implements Evaluator
{
    /**
     * @var CustomerDetailsRepository
     */
    protected $customerDetailsRepository;

    /**
     * AnniversaryEvaluator constructor.
     *
     * @param CustomerDetailsRepository $customerDetailsRepository
     */
    public function __construct(CustomerDetailsRepository $customerDetailsRepository)
    {
        $this->customerDetailsRepository = $customerDetailsRepository;
    }

    /**
     * @param Criterion $criterion
     *
     * @return array
     */
    public function evaluate(Criterion $criterion)
    {
        if (!$criterion instanceof Anniversary) {
            return [];
        }
        $from = new \DateTime();
        $to = new \DateTime('+'.$criterion->getDays().' days');

        if ($criterion->getAnniversaryType() == Anniversary::TYPE_BIRTHDAY) {
            $customers = $this->customerDetailsRepository->findByBirthdayAnniversary($from, $to);
        } elseif ($criterion->getAnniversaryType() == Anniversary::TYPE_REGISTRATION) {
            $customers = $this->customerDetailsRepository->findByCreationAnniversary($from, $to);
        } else {
            return [];
        }

        return array_map(function (CustomerDetails $customerDetails) {
            return $customerDetails->getCustomerId()->__toString();
        }, $customers);
    }

    /**
     * @param Criterion $criterion
     *
     * @return bool
     */
    public function support(Criterion $criterion)
    {
        return $criterion instanceof Anniversary;
    }
}
