<?php

namespace OpenLoyalty\Domain\Customer;

interface LevelIdProvider
{
    /**
     * @param $conditionValue
     *
     * @return string
     */
    public function findLevelIdByConditionValueWithTheBiggestReward($conditionValue);
}
