<?php

namespace OpenLoyalty\Domain\Segment\Model\Criteria;

use OpenLoyalty\Domain\Segment\CriterionId;
use Assert\Assertion as Assert;
use OpenLoyalty\Domain\Segment\Model\Criterion;

/**
 * Class BoughtMakers.
 */
class BoughtMakers extends Criterion
{
    /**
     * @var array
     */
    protected $makers = [];

    /**
     * @return array
     */
    public function getMakers()
    {
        return $this->makers;
    }

    /**
     * @param array $makers
     */
    public function setMakers($makers)
    {
        $this->makers = $makers;
    }

    public static function fromArray(array $data)
    {
        $criterion = new self(new CriterionId($data['criterionId']));
        $criterion->setMakers($data['makers']);

        return $criterion;
    }

    public static function validate(array $data)
    {
        parent::validate($data);
        Assert::keyIsset($data, 'makers');
        Assert::notBlank($data, 'makers');
        Assert::isArray($data['makers']);
    }
}
