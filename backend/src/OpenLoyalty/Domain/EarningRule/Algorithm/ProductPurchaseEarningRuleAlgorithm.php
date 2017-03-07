<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\EarningRule\Algorithm;

use OpenLoyalty\Domain\EarningRule\EarningRule;
use OpenLoyalty\Domain\EarningRule\ProductPurchaseEarningRule;

/**
 * Class ProductPurchaseEarningRuleAlgorithm.
 */
class ProductPurchaseEarningRuleAlgorithm extends AbstractRuleAlgorithm
{
    /**
     * ProductPurchaseEarningRuleAlgorithm constructor.
     */
    public function __construct()
    {
        parent::__construct(3);
    }

    /**
     * {@inheritdoc}
     */
    public function evaluate(RuleEvaluationContextInterface $context, EarningRule $rule)
    {
        if (!$rule instanceof ProductPurchaseEarningRule) {
            throw new \InvalidArgumentException(get_class($rule));
        }

        foreach ($context->getTransaction()->getItems() as $item) {
            $skuCode = $item->getSku()->getCode();
            if (in_array($skuCode, $rule->getSkuIds())) {
                $context->addProductPoints($skuCode, $rule->getPointsAmount());
            }
        }
    }
}
