<?php

namespace OpenLoyalty\Infrastructure\Account;

use OpenLoyalty\Domain\Account\CustomerId;
use OpenLoyalty\Infrastructure\Account\Exception\EarningRuleLimitExceededException;

interface EarningRuleLimitValidator
{
    /**
     * @param $earningRuleId
     * @param CustomerId $customerId
     *
     * @throws EarningRuleLimitExceededException
     */
    public function validate($earningRuleId, CustomerId $customerId);
}
