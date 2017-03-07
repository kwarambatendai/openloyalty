<?php

namespace OpenLoyalty\Domain\EarningRule;

use OpenLoyalty\Domain\Model\Label;
use OpenLoyalty\Domain\Model\SKU;
use Assert\Assertion as Assert;

/**
 * Class PointsEarningRule.
 */
class PointsEarningRule extends EarningRule
{
    /**
     * @var float
     */
    protected $pointValue;

    /**
     * @var SKU[]
     */
    protected $excludedSKUs = [];

    /**
     * @var Label[]
     */
    protected $excludedLabels = [];

    /**
     * @var bool
     */
    protected $excludeDeliveryCost = true;

    /**
     * @var float
     */
    protected $minOrderValue = 0;

    public function setFromArray(array $earningRuleData = [])
    {
        parent::setFromArray($earningRuleData);

        if (isset($earningRuleData['pointValue'])) {
            $this->pointValue = $earningRuleData['pointValue'];
        }
        if (isset($earningRuleData['excludedSKUs'])) {
            $skus = [];
            foreach ($earningRuleData['excludedSKUs'] as $sku) {
                $skus[] = SKU::deserialize($sku);
            }
            $this->excludedSKUs = $skus;
        }
        if (isset($earningRuleData['excludedLabels'])) {
            $labels = [];
            foreach ($earningRuleData['excludedLabels'] as $label) {
                if ($label == null) {
                    continue;
                }
                $labels[] = Label::deserialize($label);
            }
            $this->excludedLabels = $labels;
        }
        if (isset($earningRuleData['excludeDeliveryCost'])) {
            $this->excludeDeliveryCost = $earningRuleData['excludeDeliveryCost'];
        }
        if (isset($earningRuleData['minOrderValue'])) {
            $this->minOrderValue = $earningRuleData['minOrderValue'];
        }
    }

    /**
     * @return float
     */
    public function getPointValue()
    {
        return $this->pointValue;
    }

    /**
     * @param float $pointValue
     */
    public function setPointValue($pointValue)
    {
        $this->pointValue = $pointValue;
    }

    /**
     * @return \OpenLoyalty\Domain\Model\SKU[]
     */
    public function getExcludedSKUs()
    {
        return $this->excludedSKUs;
    }

    /**
     * @param \OpenLoyalty\Domain\Model\SKU[] $excludedSKUs
     */
    public function setExcludedSKUs($excludedSKUs)
    {
        $this->excludedSKUs = $excludedSKUs;
    }

    /**
     * @return Model\Label[]
     */
    public function getExcludedLabels()
    {
        return $this->excludedLabels;
    }

    /**
     * @param Model\Label[] $excludedLabels
     */
    public function setExcludedLabels($excludedLabels)
    {
        $this->excludedLabels = $excludedLabels;
    }

    /**
     * @return bool
     */
    public function isExcludeDeliveryCost()
    {
        return $this->excludeDeliveryCost;
    }

    /**
     * @param bool $excludeDeliveryCost
     */
    public function setExcludeDeliveryCost($excludeDeliveryCost)
    {
        $this->excludeDeliveryCost = $excludeDeliveryCost;
    }

    /**
     * @return float
     */
    public function getMinOrderValue()
    {
        return $this->minOrderValue;
    }

    /**
     * @param float $minOrderValue
     */
    public function setMinOrderValue($minOrderValue)
    {
        $this->minOrderValue = $minOrderValue;
    }

    public static function validateRequiredData(array $earningRuleData = [])
    {
        parent::validateRequiredData($earningRuleData);
        Assert::keyIsset($earningRuleData, 'pointValue');
        Assert::notBlank($earningRuleData['pointValue']);
    }
}
