<?php

namespace OpenLoyalty\Domain\Segment\Model\Criteria;

use OpenLoyalty\Domain\Segment\CriterionId;
use OpenLoyalty\Domain\Segment\Model\Criterion;
use Assert\Assertion as Assert;

/**
 * Class BoughtInPos.
 */
class BoughtInPos extends Criterion
{
    /**
     * @var array
     */
    protected $posIds = [];

    /**
     * @return array
     */
    public function getPosIds()
    {
        return $this->posIds;
    }

    /**
     * @param array $posIds
     */
    public function setPosIds($posIds)
    {
        $this->posIds = $posIds;
    }

    public static function fromArray(array $data)
    {
        $criterion = new self(new CriterionId($data['criterionId']));
        $criterion->setPosIds($data['posIds']);

        return $criterion;
    }

    public static function validate(array $data)
    {
        parent::validate($data);
        Assert::keyIsset($data, 'posIds');
        Assert::notBlank($data, 'posIds');
        Assert::isArray($data['posIds']);
        Assert::allString($data['posIds']);
        Assert::allUuid($data['posIds']);
    }
}
