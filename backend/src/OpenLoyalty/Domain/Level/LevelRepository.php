<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Level;

interface LevelRepository
{
    public function byId(LevelId $levelId);

    public function findAll();

    public function findAllActive();

    public function findOneByRewardPercent($percent);

    public function save(Level $level);

    public function remove(Level $level);

    public function findAllPaginated($page = 1, $perPage = 10, $sortField = 'levelId', $direction = 'DESC');

    public function countTotal();

    public function findLevelByConditionValueWithTheBiggestReward($conditionValue);

    public function findNextLevelByConditionValueWithTheBiggestReward($conditionValue, $currentLevelValue);
}
