<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\EarningRule;

use Assert\Assertion as Assert;

/**
 * Class EarningRule.
 */
abstract class EarningRule
{
    const TYPE_POINTS = 'points';
    const TYPE_EVENT = 'event';
    const TYPE_CUSTOM_EVENT = 'custom_event';
    const TYPE_PRODUCT_PURCHASE = 'product_purchase';
    const TYPE_MULTIPLY_FOR_PRODUCT = 'multiply_for_product';

    const TYPE_MAP = [
        self::TYPE_EVENT => EventEarningRule::class,
        self::TYPE_CUSTOM_EVENT => CustomEventEarningRule::class,
        self::TYPE_POINTS => PointsEarningRule::class,
        self::TYPE_PRODUCT_PURCHASE => ProductPurchaseEarningRule::class,
        self::TYPE_MULTIPLY_FOR_PRODUCT => MultiplyPointsForProductEarningRule::class,
    ];

    /**
     * @var EarningRuleId
     */
    protected $earningRuleId;

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
     * @var \DateTime
     */
    protected $startAt;

    /**
     * @var \DateTime
     */
    protected $endAt;

    /**
     * @var bool
     */
    protected $allTimeActive = false;

    /**
     * @var EarningRuleUsage[]
     */
    protected $usages;

    /**
     * EarningRule constructor.
     *
     * @param EarningRuleId $earningRuleId
     * @param array         $earningRuleData
     */
    public function __construct(EarningRuleId $earningRuleId, array $earningRuleData = [])
    {
        $this->earningRuleId = $earningRuleId;
        $this->setFromArray($earningRuleData);
    }

    public function setFromArray(array $earningRuleData = [])
    {
        if (isset($earningRuleData['name'])) {
            $this->name = $earningRuleData['name'];
        }
        if (isset($earningRuleData['description'])) {
            $this->description = $earningRuleData['description'];
        }
        if (isset($earningRuleData['active'])) {
            $this->active = $earningRuleData['active'];
        }
        if (isset($earningRuleData['allTimeActive'])) {
            $this->allTimeActive = $earningRuleData['allTimeActive'];
        }
        if (isset($earningRuleData['startAt'])) {
            $tmp = new \DateTime();
            $tmp->setTimestamp($earningRuleData['startAt']);
            $this->startAt = $tmp;
        }
        if (isset($earningRuleData['endAt'])) {
            $tmp = new \DateTime();
            $tmp->setTimestamp($earningRuleData['endAt']);
            $this->endAt = $tmp;
        }
    }

    /**
     * @return EarningRuleId
     */
    public function getEarningRuleId()
    {
        return $this->earningRuleId;
    }

    /**
     * @param EarningRuleId $earningRuleId
     */
    public function setEarningRuleId($earningRuleId)
    {
        $this->earningRuleId = $earningRuleId;
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
     * @return bool
     */
    public function isAllTimeActive()
    {
        return $this->allTimeActive;
    }

    /**
     * @param bool $allTimeActive
     */
    public function setAllTimeActive($allTimeActive)
    {
        $this->allTimeActive = $allTimeActive;
    }

    public static function validateRequiredData(array $earningRuleData = [])
    {
        Assert::keyIsset($earningRuleData, 'name');
        Assert::keyIsset($earningRuleData, 'description');
        if (!isset($earningRuleData['allTimeActive']) || !$earningRuleData['allTimeActive']) {
            Assert::keyIsset($earningRuleData, 'startAt');
            Assert::keyIsset($earningRuleData, 'endAt');
            Assert::notBlank($earningRuleData['startAt']);
            Assert::notBlank($earningRuleData['endAt']);
        }

        Assert::notBlank($earningRuleData['name']);
        Assert::notBlank($earningRuleData['description']);
    }

    public function addUsage(EarningRuleUsage $usage)
    {
        $usage->setEarningRule($this);
        $this->usages[] = $usage;
    }

    /**
     * @return EarningRuleUsage[]
     */
    public function getUsages()
    {
        return $this->usages;
    }

    /**
     * @param EarningRuleUsage[] $usages
     */
    public function setUsages($usages)
    {
        $this->usages = $usages;
    }
}
