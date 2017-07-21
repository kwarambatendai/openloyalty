<?php

namespace OpenLoyalty\Domain\EarningRule;

use Assert\Assertion as Assert;

/**
 * Class ReferralEarningRule.
 */
class ReferralEarningRule extends EarningRule
{
    const EVENT_REGISTER = 'register';
    const EVENT_FIRST_PURCHASE = 'first_purchase';
    const EVENT_EVERY_PURCHASE = 'every_purchase';
    const TYPE_REFERRER = 'referrer';
    const TYPE_REFERRED = 'referred';
    const TYPE_BOTH = 'both';

    /**
     * @var string
     */
    protected $eventName;

    /**
     * @var string
     */
    protected $rewardType;

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

        if (isset($earningRuleData['rewardType'])) {
            $this->rewardType = $earningRuleData['rewardType'];
        }
    }

    public static function validateRequiredData(array $earningRuleData = [])
    {
        parent::validateRequiredData($earningRuleData);
        Assert::keyIsset($earningRuleData, 'eventName');
        Assert::keyIsset($earningRuleData, 'pointsAmount');
        Assert::keyIsset($earningRuleData, 'rewardType');
        Assert::notBlank($earningRuleData['eventName']);
        Assert::notBlank($earningRuleData['pointsAmount']);
        Assert::notBlank($earningRuleData['rewardType']);
        Assert::min($earningRuleData['pointsAmount'], 0);
        Assert::choice($earningRuleData['eventName'], [
            self::EVENT_EVERY_PURCHASE,
            self::EVENT_FIRST_PURCHASE,
            self::EVENT_REGISTER,
        ]);
        Assert::choice($earningRuleData['rewardType'], [
            self::TYPE_BOTH,
            self::TYPE_REFERRED,
            self::TYPE_REFERRER,
        ]);
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * @return string
     */
    public function getRewardType()
    {
        return $this->rewardType;
    }

    /**
     * @return float
     */
    public function getPointsAmount()
    {
        return (float) $this->pointsAmount;
    }

    /**
     * @param string $eventName
     */
    public function setEventName($eventName)
    {
        $this->eventName = $eventName;
    }

    /**
     * @param string $rewardType
     */
    public function setRewardType($rewardType)
    {
        $this->rewardType = $rewardType;
    }

    /**
     * @param float $pointsAmount
     */
    public function setPointsAmount($pointsAmount)
    {
        $this->pointsAmount = $pointsAmount;
    }
}
