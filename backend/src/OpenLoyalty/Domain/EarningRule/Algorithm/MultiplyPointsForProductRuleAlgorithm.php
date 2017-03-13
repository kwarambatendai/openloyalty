<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\EarningRule\Algorithm;

use OpenLoyalty\Domain\EarningRule\EarningRule;
use OpenLoyalty\Domain\EarningRule\MultiplyPointsForProductEarningRule;
use OpenLoyalty\Domain\Transaction\Model\Item;

/**
 * Class MultiplyPointsForProductRuleAlgorithm.
 */
class MultiplyPointsForProductRuleAlgorithm extends AbstractRuleAlgorithm
{
    /**
     * MultiplyPointsForProductRuleAlgorithm constructor.
     */
    public function __construct()
    {
        parent::__construct(2);
    }

    /**
     * {@inheritdoc}
     */
    public function evaluate(RuleEvaluationContextInterface $context, EarningRule $rule)
    {
        if (!$rule instanceof MultiplyPointsForProductEarningRule) {
            throw new \InvalidArgumentException(get_class($rule));
        }

        foreach ($context->getTransaction()->getItems() as $item) {
            $sku = $item->getSku()->getCode();

            if (in_array($sku, $rule->getSkuIds()) || $this->getItemHasLabel($rule, $item)) {
                $context->setProductPoints($sku, $context->getProductPoints($sku) * $rule->getMultiplier());
            }
        }
    }

    /**
     * @param MultiplyPointsForProductEarningRule $rule
     * @param Item                                $item
     *
     * @return bool
     */
    protected function getItemHasLabel(MultiplyPointsForProductEarningRule $rule, Item $item)
    {
        foreach ($rule->getLabels() as $label) {
            foreach ($item->getLabels() as $itemLabel) {
                if ($itemLabel->getKey() == $label->getKey() && $itemLabel->getValue() == $label->getValue()) {
                    return true;
                }
            }
        }

        return false;
    }
}
