<?php

namespace OpenLoyalty\Domain\EarningRule\Command;

use OpenLoyalty\Domain\EarningRule\EarningRuleId;
use OpenLoyalty\Domain\EarningRule\Model\UsageSubject;

/**
 * Class UseCustomEventEarningRule.
 */
class UseCustomEventEarningRule extends EarningRuleCommand
{
    /**
     * @var UsageSubject
     */
    protected $subject;

    public function __construct(EarningRuleId $earningRuleId, UsageSubject $subject)
    {
        parent::__construct($earningRuleId);
        $this->subject = $subject;
    }

    /**
     * @return UsageSubject
     */
    public function getSubject()
    {
        return $this->subject;
    }
}
