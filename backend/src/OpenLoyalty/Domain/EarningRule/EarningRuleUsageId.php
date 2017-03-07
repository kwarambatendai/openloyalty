<?php

namespace OpenLoyalty\Domain\EarningRule;

use OpenLoyalty\Domain\Identifier;
use Assert\Assertion as Assert;

/**
 * Class EarningRuleUsageId.
 */
class EarningRuleUsageId implements Identifier
{
    /**
     * @var string
     */
    private $earningRuleUsageId;

    /**
     * EarningRuleId constructor.
     *
     * @param $earningRuleUsageId
     */
    public function __construct($earningRuleUsageId)
    {
        Assert::string($earningRuleUsageId);
        Assert::uuid($earningRuleUsageId);

        $this->earningRuleUsageId = $earningRuleUsageId;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->earningRuleUsageId;
    }
}
