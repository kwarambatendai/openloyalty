<?php

namespace OpenLoyalty\Domain\EarningRule\Algorithm;

/**
 * Interface EarningRuleAlgorithmFactoryInterface.
 */
interface EarningRuleAlgorithmFactoryInterface
{
    /**
     * @param $class
     *
     * @return EarningRuleAlgorithmInterface
     */
    public function getAlgorithm($class);
}
