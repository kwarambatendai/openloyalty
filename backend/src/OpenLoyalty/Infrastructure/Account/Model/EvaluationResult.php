<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Infrastructure\Account\Model;

/**
 * Class EvaluationResult.
 */
class EvaluationResult
{
    /**
     * @var string
     */
    protected $earningRuleId = null;

    /**
     * @var int
     */
    protected $points = null;

    /**
     * EvaluationResult constructor.
     *
     * @param string $earningRuleId
     * @param int    $points
     */
    public function __construct($earningRuleId, $points)
    {
        $this->earningRuleId = $earningRuleId;
        $this->points = $points;
    }

    /**
     * @return string
     */
    public function getEarningRuleId()
    {
        return $this->earningRuleId;
    }

    /**
     * @return int
     */
    public function getPoints()
    {
        return $this->points;
    }
}
