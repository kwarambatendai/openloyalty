<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Level;

use OpenLoyalty\Domain\Level\Model\Reward;
use Assert\Assertion as Assert;

/**
 * Class SpecialReward.
 */
class SpecialReward extends Reward
{
    /**
     * @var SpecialRewardId
     */
    protected $specialRewardId;

    /**
     * @var bool
     */
    protected $active = false;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $startAt;

    /**
     * @var \DateTime
     */
    protected $endAt;

    /**
     * @var Level
     */
    protected $level;

    /**
     * SpecialReward constructor.
     *
     * @param SpecialRewardId $specialRewardId
     * @param Level           $level
     * @param string          $name
     * @param                 $value
     * @param                 $code
     */
    public function __construct(SpecialRewardId $specialRewardId, Level $level, $name, $value, $code)
    {
        parent::__construct($name, $value, $code);
        Assert::notBlank($level);
        $this->specialRewardId = $specialRewardId;
        $this->level = $level;
        $this->createdAt = new \DateTime();
    }

    /**
     * @return SpecialRewardId
     */
    public function getSpecialRewardId()
    {
        return $this->specialRewardId;
    }

    /**
     * @param SpecialRewardId $specialRewardId
     */
    public function setSpecialRewardId($specialRewardId)
    {
        $this->specialRewardId = $specialRewardId;
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
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * @param \DateTime $startAt
     */
    public function setStartAt($startAt)
    {
        $this->startAt = $startAt;
    }

    /**
     * @return \DateTime
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    /**
     * @param \DateTime $endAt
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;
    }

    /**
     * @return Level
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param Level $level
     */
    public function setLevel($level)
    {
        Assert::notBlank($level);
        $this->level = $level;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        Assert::notBlank($name);
        $this->name = $name;
    }

    /**
     * @param float $value
     */
    public function setValue($value)
    {
        Assert::notBlank($value);
        $this->value = $value;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        Assert::notBlank($code);
        $this->code = $code;
    }
}
