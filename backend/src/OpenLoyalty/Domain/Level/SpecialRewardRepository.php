<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Level;

interface SpecialRewardRepository
{
    public function byId(SpecialRewardId $specialRewardId);

    public function findAll();

    public function save(SpecialReward $specialReward);

    public function remove(SpecialReward $specialReward);
}
