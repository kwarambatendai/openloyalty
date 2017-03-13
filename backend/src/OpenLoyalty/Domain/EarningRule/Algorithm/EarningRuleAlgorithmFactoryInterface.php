<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
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
