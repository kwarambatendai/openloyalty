<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
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
