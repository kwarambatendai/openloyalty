<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\LevelBundle\Model;

/**
 * Class Level.
 */
class Level extends \OpenLoyalty\Domain\Level\Level
{
    public function __construct()
    {
    }

    /**
     * @var Reward
     */
    protected $reward;

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
        $this->reward = $reward;
    }

    public function toArray()
    {
        $specialRewards = array_map(function (SpecialReward $specialReward) {
            return $specialReward->toArray();
        }, $this->getSpecialRewards());

        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'active' => $this->isActive(),
            'conditionValue' => $this->getConditionValue(),
            'reward' => $this->getReward()->toArray(),
            'specialRewards' => $specialRewards,
            'minOrder' => $this->getMinOrder(),
        ];
    }
}
