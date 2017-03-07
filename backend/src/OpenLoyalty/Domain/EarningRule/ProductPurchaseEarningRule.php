<?php

namespace OpenLoyalty\Domain\EarningRule;

use Assert\Assertion as Assert;

/**
 * Class ProductPurchaseEarningRule.
 */
class ProductPurchaseEarningRule extends EarningRule
{
    /**
     * @var array
     */
    protected $skuIds = [];

    /**
     * @var int
     */
    protected $pointsAmount;

    public function setFromArray(array $earningRuleData = [])
    {
        parent::setFromArray($earningRuleData);

        if (isset($earningRuleData['skuIds'])) {
            $this->skuIds = $earningRuleData['skuIds'];
        }
        if (isset($earningRuleData['pointsAmount'])) {
            $this->pointsAmount = $earningRuleData['pointsAmount'];
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
     * @return int
     */
    public function getPointsAmount()
    {
        return $this->pointsAmount;
    }

    /**
     * @param int $pointsAmount
     */
    public function setPointsAmount($pointsAmount)
    {
        $this->pointsAmount = $pointsAmount;
    }

    public static function validateRequiredData(array $earningRuleData = [])
    {
        parent::validateRequiredData($earningRuleData);
        Assert::keyIsset($earningRuleData, 'skuIds');
        Assert::keyIsset($earningRuleData, 'pointsAmount');
        Assert::notBlank($earningRuleData['skuIds']);
        Assert::isArray($earningRuleData['skuIds']);
        Assert::notBlank($earningRuleData['pointsAmount']);
    }
}
