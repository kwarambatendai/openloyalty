<?php

namespace OpenLoyalty\Domain\EarningRule\Exception;

/**
 * Class CustomEventEarningRuleAlreadyExistsException.
 */
class CustomEventEarningRuleAlreadyExistsException extends EarningRuleException
{
    protected $message = 'Earning rule with such event name already exist';
}
