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
 * Class BoughtSKUs.
 */
class BoughtSKUs extends Criterion
{
    /**
     * @var array
     */
    protected $skuIds = [];

    /**
     * @return array
     */
    public function getSkuIds()
    {
        return $this->skuIds;
    }

    /**
     * @param array $skuIds
     */
    public function setSkuIds($skuIds)
    {
        $this->skuIds = $skuIds;
    }

    public static function fromArray(array $data)
    {
        $criterion = new self(new CriterionId($data['criterionId']));
        $criterion->setSkuIds($data['skuIds']);

        return $criterion;
    }

    public static function validate(array $data)
    {
        parent::validate($data);
        Assert::keyIsset($data, 'skuIds');
        Assert::notBlank($data, 'skuIds');
        Assert::isArray($data['skuIds']);
    }
}
