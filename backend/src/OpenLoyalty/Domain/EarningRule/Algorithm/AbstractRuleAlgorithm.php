<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\EarningRule\Algorithm;

/**
 * Class AbstractRuleAlgorithm.
 */
abstract class AbstractRuleAlgorithm implements EarningRuleAlgorithmInterface
{
    /**
     * @var int
     */
    protected $priority;

    /**
     * AbstractRuleAlgorithm constructor.
     *
     * @param int $priority
     */
    public function __construct($priority = 0)
    {
        $this->priority = $priority;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->priority;
    }
}
