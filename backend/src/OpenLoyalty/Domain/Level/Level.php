<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Level;

use OpenLoyalty\Domain\Level\Model\Reward;
use Assert\Assertion as Assert;

/**
 * Class Level.
 */
class Level
{
    /**
     * @var LevelId
     */
    protected $levelId;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var bool
     */
    protected $active = false;

    /**
     * @var float
     */
    protected $conditionValue;

    /**
     * @var Reward
     */
    protected $reward;

    /**
     * @var SpecialReward[]
     */
    protected $specialRewards = [];

    /**
     * @var int
     */
    protected $customersCount = 0;

    /**
     * @var float
     */
    protected $minOrder;

    /**
     * Level constructor.
     *
     * @param LevelId $levelId
     * @param string  $name
     * @param         $conditionValue
     * @param string  $description
     */
    public function __construct(LevelId $levelId, $name, $conditionValue, $description = null)
    {
        Assert::notEmpty($levelId);
        Assert::notEmpty($name);
        Assert::greaterOrEqualThan($conditionValue, 0);

        $this->levelId = $levelId;
        $this->name = $name;
        $this->conditionValue = $conditionValue;
        $this->description = $description;
    }

    /**
     * @return LevelId
     */
    public function getLevelId()
    {
        return $this->levelId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        Assert::notEmpty($name);
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return Reward
     */
    public function getReward()
    {
        return $this->reward;
    }

    /**
     * @param Reward $reward
     */
    public function setReward($reward)
    {
        Assert::notEmpty($reward);
        $this->reward = $reward;
    }

    /**
     * @return array
     */
    public function getSpecialRewards()
    {
        return $this->specialRewards;
    }

    /**
     * @param array $specialRewards
     */
    public function setSpecialRewards($specialRewards)
    {
        $this->specialRewards = $specialRewards;
    }

    public function addSpecialReward(SpecialReward $specialReward)
    {
        $this->specialRewards[] = $specialReward;
    }

    /**
     * @return float
     */
    public function getConditionValue()
    {
        return $this->conditionValue;
    }

    /**
     * @param float $conditionValue
     */
    public function setConditionValue($conditionValue)
    {
        $this->conditionValue = $conditionValue;
    }

    /**
     * @return int
     */
    public function getCustomersCount()
    {
        return $this->customersCount;
    }

    /**
     * @param int $customersCount
     */
    public function setCustomersCount($customersCount)
    {
        $this->customersCount = $customersCount;
    }

    /**
     * @return float
     */
    public function getMinOrder()
    {
        return $this->minOrder;
    }

    /**
     * @param float $minOrder
     */
    public function setMinOrder($minOrder)
    {
        $this->minOrder = $minOrder;
    }
}
