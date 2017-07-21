<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\EarningRule;

use Assert\Assertion as Assert;

/**
 * Class CustomEventEarningRule.
 */
class CustomEventEarningRule extends EarningRule
{
    /**
     * @var string
     */
    protected $eventName;

    /**
     * @var float
     */
    protected $pointsAmount;

    /**
     * @var EarningRuleLimit
     */
    protected $limit;

    public function setFromArray(array $earningRuleData = [])
    {
        parent::setFromArray($earningRuleData);

        if (isset($earningRuleData['eventName'])) {
            $this->eventName = $earningRuleData['eventName'];
        }
        if (isset($earningRuleData['pointsAmount'])) {
            $this->pointsAmount = $earningRuleData['pointsAmount'];
        }

        if (isset($earningRuleData['limit'])) {
            $this->limit = EarningRuleLimit::fromArray($earningRuleData['limit']);
        }
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * @param string $eventName
     */
    public function setEventName($eventName)
    {
        $this->eventName = $eventName;
    }

    /**
     * @return float
     */
    public function getPointsAmount()
    {
        return (float) $this->pointsAmount;
    }

    /**
     * @param float $pointsAmount
     */
    public function setPointsAmount($pointsAmount)
    {
        $this->pointsAmount = $pointsAmount;
    }

    public static function validateRequiredData(array $earningRuleData = [])
    {
        parent::validateRequiredData($earningRuleData);
        Assert::keyIsset($earningRuleData, 'eventName');
        Assert::keyIsset($earningRuleData, 'pointsAmount');
        Assert::notBlank($earningRuleData['eventName']);
        Assert::notBlank($earningRuleData['pointsAmount']);
        Assert::min($earningRuleData['pointsAmount'], 0);
        if (isset($earningRuleData['limit'])) {
            EarningRuleLimit::validateRequiredData($earningRuleData['limit']);
        }
    }

    /**
     * @return EarningRuleLimit
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param EarningRuleLimit $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }
}
