<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\EarningRule\Command;

use OpenLoyalty\Domain\EarningRule\EarningRuleId;

/**
 * Class EarningRuleCommand.
 */
class EarningRuleCommand
{
    /**
     * @var EarningRuleId
     */
    protected $earningRuleId;

    /**
     * EarningRuleCommand constructor.
     *
     * @param EarningRuleId $earningRuleId
     */
    public function __construct(EarningRuleId $earningRuleId)
    {
        $this->earningRuleId = $earningRuleId;
    }

    /**
     * @return EarningRuleId
     */
    public function getEarningRuleId()
    {
        return $this->earningRuleId;
    }
}
