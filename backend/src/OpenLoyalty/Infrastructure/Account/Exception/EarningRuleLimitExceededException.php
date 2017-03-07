<?php

namespace OpenLoyalty\Infrastructure\Account\Exception;

/**
 * Class EarningRuleLimitExceededException.
 */
class EarningRuleLimitExceededException extends \Exception
{
    protected $message = 'Limit exceeded';
}
