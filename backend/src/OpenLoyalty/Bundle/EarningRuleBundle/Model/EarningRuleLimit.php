<?php

namespace OpenLoyalty\Bundle\EarningRuleBundle\Model;

use OpenLoyalty\Domain\EarningRule\EarningRuleLimit as BaseLimit;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class EarningRuleLimit.
 */
class EarningRuleLimit extends BaseLimit
{
    /**
     * @param ExecutionContextInterface $context
     * @Assert\Callback()
     */
    public function validate(ExecutionContextInterface $context)
    {
        if (!$this->active) {
            return;
        }
        if (null == $this->limit || $this->limit < 0) {
            $context->buildViolation('This value must be greater than 0')->atPath('limit')->addViolation();
        }

        if (!in_array($this->period, [
            static::PERIOD_DAY, static::PERIOD_WEEK, static::PERIOD_MONTH,
        ])) {
            $context->buildViolation('This value is not valid.')->atPath('period')->addViolation();
        }
    }

    /**
     * @param bool $active
     */
    public function setActive($active)
    {
        $this->active = $active;
        if (!$active) {
            $this->period = null;
            $this->limit = null;
        }
    }
}
