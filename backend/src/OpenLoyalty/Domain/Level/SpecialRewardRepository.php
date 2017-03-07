<?php

namespace OpenLoyalty\Domain\Level;

interface SpecialRewardRepository
{
    public function byId(SpecialRewardId $specialRewardId);

    public function findAll();

    public function save(SpecialReward $specialReward);

    public function remove(SpecialReward $specialReward);
}
