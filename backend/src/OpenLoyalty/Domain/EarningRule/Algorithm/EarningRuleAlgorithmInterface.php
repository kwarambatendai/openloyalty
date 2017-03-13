<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\EarningRule\Algorithm;

use OpenLoyalty\Domain\EarningRule\EarningRule;

/**
 * Interface EarningRuleAlgorithmInterface.
 */
interface EarningRuleAlgorithmInterface
{
    /**
     * @param RuleEvaluationContextInterface $context
     * @param EarningRule                    $rule
     */
    public function evaluate(RuleEvaluationContextInterface $context, EarningRule $rule);

    /**
     * @return int
     */
    public function getPriority();
}
