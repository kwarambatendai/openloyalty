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
 * Class AverageTransactionAmount.
 */
class AverageTransactionAmount extends Criterion
{
    /**
     * @var float
     */
    protected $fromAmount;

    /**
     * @var float
     */
    protected $toAmount;

    /**
     * @return float
     */
    public function getFromAmount()
    {
        return $this->fromAmount;
    }

    /**
     * @param float $fromAmount
     */
    public function setFromAmount($fromAmount)
    {
        $this->fromAmount = $fromAmount;
    }

    /**
     * @return float
     */
    public function getToAmount()
    {
        return $this->toAmount;
    }

    /**
     * @param float $toAmount
     */
    public function setToAmount($toAmount)
    {
        $this->toAmount = $toAmount;
    }

    public static function fromArray(array $data)
    {
        $criterion = new self(new CriterionId($data['criterionId']));
        $criterion->setFromAmount($data['fromAmount']);
        $criterion->setToAmount($data['toAmount']);

        return $criterion;
    }

    public static function validate(array $data)
    {
        parent::validate($data);
        Assert::keyIsset($data, 'fromAmount');
        Assert::keyIsset($data, 'toAmount');
        Assert::notBlank($data, 'fromAmount');
        Assert::notBlank($data, 'toAmount');
    }
}
