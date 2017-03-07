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
 * Class LastPurchaseNDaysBefore.
 */
class LastPurchaseNDaysBefore extends Criterion
{
    /**
     * @var int
     */
    protected $days;

    public static function fromArray(array $data)
    {
        $criterion = new self(new CriterionId($data['criterionId']));
        $criterion->setDays($data['days']);

        return $criterion;
    }

    public static function validate(array $data)
    {
        parent::validate($data);
        Assert::keyIsset($data, 'days');
        Assert::notBlank($data, 'days');
        Assert::integer($data['days']);
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
}
