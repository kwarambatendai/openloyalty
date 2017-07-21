<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\EarningRule;

use Assert\Assertion as Assert;

/**
 * Class EventEarningRule.
 */
class EventEarningRule extends EarningRule
{
    /**
     * @var string
     */
    protected $eventName;

    /**
     * @var float
     */
    protected $pointsAmount;

    public function setFromArray(array $earningRuleData = [])
    {
        parent::setFromArray($earningRuleData);

        if (isset($earningRuleData['eventName'])) {
            $this->eventName = $earningRuleData['eventName'];
        }
        if (isset($earningRuleData['pointsAmount'])) {
            $this->pointsAmount = $earningRuleData['pointsAmount'];
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
    }
}
