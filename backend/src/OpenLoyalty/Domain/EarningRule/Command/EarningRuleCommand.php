<?php

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
