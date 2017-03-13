<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\EarningRule;

use Assert\Assertion as Assert;
use OpenLoyalty\Domain\Model\Label;

/**
 * Class MultiplyPointsForProductEarningRule.
 */
class MultiplyPointsForProductEarningRule extends EarningRule
{
    /**
     * @var array
     */
    protected $skuIds = [];

    /**
     * @var Label[]
     */
    protected $labels = [];

    /**
     * @var float
     */
    protected $multiplier;

    public function setFromArray(array $earningRuleData = [])
    {
        parent::setFromArray($earningRuleData);

        if (isset($earningRuleData['skuIds'])) {
            $this->skuIds = $earningRuleData['skuIds'];
        }
        if (isset($earningRuleData['multiplier'])) {
            $this->multiplier = $earningRuleData['multiplier'];
        }
        if (isset($earningRuleData['labels'])) {
            $labels = [];
            foreach ($earningRuleData['labels'] as $label) {
                if ($label == null) {
                    continue;
                }
                $labels[] = Label::deserialize($label);
            }
            $this->labels = $labels;
        }
    }

    /**
     * @return array
     */
    public function getSkuIds()
    {
        return $this->skuIds;
    }

    /**
     * @param array $skuIds
     */
    public function setSkuIds($skuIds)
    {
        $this->skuIds = $skuIds;
    }

    /**
     * @return float
     */
    public function getMultiplier()
    {
        return $this->multiplier;
    }

    /**
     * @param float $multiplier
     */
    public function setMultiplier($multiplier)
    {
        $this->multiplier = $multiplier;
    }

    /**
     * @return Label[]
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @param Label[] $labels
     */
    public function setLabels(array $labels)
    {
        $this->labels = $labels;
    }

    public static function validateRequiredData(array $earningRuleData = [])
    {
        parent::validateRequiredData($earningRuleData);
        Assert::keyIsset($earningRuleData, 'skuIds');
        Assert::keyIsset($earningRuleData, 'multiplier');
        Assert::isArray($earningRuleData['skuIds']);
        Assert::notBlank($earningRuleData['multiplier']);
    }
}
