<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Segment\Segmentation\CriteriaEvaluators;

use OpenLoyalty\Domain\Segment\Model\Criterion;

interface Evaluator
{
    /**
     * @param Criterion $criterion
     *
     * @return array
     */
    public function evaluate(Criterion $criterion);

    /**
     * @param Criterion $criterion
     *
     * @return bool
     */
    public function support(Criterion $criterion);
}
