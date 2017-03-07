<?php

namespace OpenLoyalty\Domain\Level\Command;

use OpenLoyalty\Domain\Level\LevelId;
use Assert\Assertion as Assert;

/**
 * Class CreateLevel.
 */
class CreateLevel extends LevelCommand
{
    protected $levelData;

    /**
     * CreateLevel constructor.
     *
     * @param LevelId $levelId
     * @param $levelData
     */
    public function __construct(LevelId $levelId, $levelData)
    {
        Assert::notEmpty($levelData['name']);
        Assert::greaterOrEqualThan($levelData['conditionValue'], 0);
        Assert::notEmpty($levelData['reward']);

        parent::__construct($levelId);

        $this->levelData = $levelData;
    }

    /**
     * @return mixed
     */
    public function getLevelData()
    {
        return $this->levelData;
    }
}
