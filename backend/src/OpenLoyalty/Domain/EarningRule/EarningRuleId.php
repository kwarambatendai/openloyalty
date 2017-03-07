<?php

namespace OpenLoyalty\Domain\EarningRule;

use OpenLoyalty\Domain\Identifier;
use Assert\Assertion as Assert;

/**
 * Class EarningRuleId.
 */
class EarningRuleId implements Identifier
{
    /**
     * @var string
     */
    private $earningRuleId;

    /**
     * EarningRuleId constructor.
     *
     * @param $earningRuleId
     */
    public function __construct($earningRuleId)
    {
        Assert::string($earningRuleId);
        Assert::uuid($earningRuleId);

        $this->earningRuleId = $earningRuleId;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->earningRuleId;
    }
}
