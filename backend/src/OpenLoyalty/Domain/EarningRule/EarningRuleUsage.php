<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\EarningRule;

use OpenLoyalty\Domain\EarningRule\Model\UsageSubject;

/**
 * Class EarningRuleUsage.
 */
class EarningRuleUsage
{
    /**
     * @var EarningRuleUsageId
     */
    protected $earningRuleUsageId;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @var UsageSubject
     */
    protected $subject;

    /**
     * @var EarningRule
     */
    protected $earningRule;

    /**
     * EarningRuleUsage constructor.
     *
     * @param EarningRuleUsageId $earningRuleUsageId
     * @param UsageSubject       $subject
     * @param EarningRule        $earningRule
     */
    public function __construct(EarningRuleUsageId $earningRuleUsageId, UsageSubject $subject, EarningRule $earningRule)
    {
        $this->earningRuleUsageId = $earningRuleUsageId;
        $this->subject = $subject;
        $this->earningRule = $earningRule;
        $this->date = new \DateTime();
    }

    /**
     * @return EarningRuleUsageId
     */
    public function getEarningRuleUsageId()
    {
        return $this->earningRuleUsageId;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return UsageSubject
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param UsageSubject $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return EarningRule
     */
    public function getEarningRule()
    {
        return $this->earningRule;
    }

    /**
     * @param EarningRule $earningRule
     */
    public function setEarningRule($earningRule)
    {
        $this->earningRule = $earningRule;
    }
}
