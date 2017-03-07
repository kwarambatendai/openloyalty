<?php

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
