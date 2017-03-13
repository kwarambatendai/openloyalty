<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UserBundle\Service;

use OpenLoyalty\Domain\Customer\LevelIdProvider;
use OpenLoyalty\Domain\Level\Level;
use OpenLoyalty\Domain\Level\LevelRepository;

/**
 * Class OloyLevelIdProvider.
 */
class OloyLevelIdProvider implements LevelIdProvider
{
    /**
     * @var LevelRepository
     */
    protected $levelRepository;

    /**
     * OloyLevelIdProvider constructor.
     *
     * @param LevelRepository $levelRepository
     */
    public function __construct(LevelRepository $levelRepository)
    {
        $this->levelRepository = $levelRepository;
    }

    /**
     * @param $conditionValue
     *
     * @return string
     */
    public function findLevelIdByConditionValueWithTheBiggestReward($conditionValue)
    {
        /** @var Level $level */
        $level = $this->levelRepository->findLevelByConditionValueWithTheBiggestReward($conditionValue);

        if (!$level) {
            return;
        }

        return $level->getLevelId()->__toString();
    }
}
