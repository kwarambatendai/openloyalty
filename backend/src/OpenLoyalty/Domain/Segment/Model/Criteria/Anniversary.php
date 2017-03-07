<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Segment\Model\Criteria;

use OpenLoyalty\Domain\Segment\CriterionId;
use OpenLoyalty\Domain\Segment\Model\Criterion;
use Assert\Assertion as Assert;

/**
 * Class Anniversary.
 */
class Anniversary extends Criterion
{
    const TYPE_BIRTHDAY = 'birthday';
    const TYPE_REGISTRATION = 'registration';

    /**
     * @var string
     */
    protected $anniversaryType;

    /**
     * @var int
     */
    protected $days = 1;

    /**
     * @return string
     */
    public function getAnniversaryType()
    {
        return $this->anniversaryType;
    }

    /**
     * @param string $anniversaryType
     */
    public function setAnniversaryType($anniversaryType)
    {
        $this->anniversaryType = $anniversaryType;
    }

    /**
     * @return int
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * @param int $days
     */
    public function setDays($days)
    {
        $this->days = $days;
    }

    public static function fromArray(array $data)
    {
        $criterion = new self(new CriterionId($data['criterionId']));
        $criterion->setDays($data['days']);
        $criterion->setAnniversaryType($data['anniversaryType']);

        return $criterion;
    }

    public static function validate(array $data)
    {
        parent::validate($data);
        Assert::keyIsset($data, 'days');
        Assert::keyIsset($data, 'anniversaryType');
        Assert::notBlank($data, 'days');
        Assert::notBlank($data, 'anniversaryType');
        Assert::integer($data['days']);
        Assert::min($data['days'], 1);
        Assert::string($data['anniversaryType']);
        Assert::choice($data['anniversaryType'], [self::TYPE_BIRTHDAY, self::TYPE_REGISTRATION]);
    }
}
