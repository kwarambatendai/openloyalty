<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Segment\Model\Criteria;

use OpenLoyalty\Domain\Segment\CriterionId;
use Assert\Assertion as Assert;
use OpenLoyalty\Domain\Segment\Model\Criterion;

/**
 * Class BoughtLabels.
 */
class BoughtLabels extends Criterion
{
    /**
     * @var array
     */
    protected $labels = [];

    /**
     * @return array
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @param array $labels
     */
    public function setLabels($labels)
    {
        $this->labels = $labels;
    }

    public static function fromArray(array $data)
    {
        $criterion = new self(new CriterionId($data['criterionId']));
        $criterion->setLabels($data['labels']);

        return $criterion;
    }

    public static function validate(array $data)
    {
        parent::validate($data);
        Assert::keyIsset($data, 'labels');
        Assert::notBlank($data, 'labels');
        Assert::isArray($data['labels']);
    }
}
