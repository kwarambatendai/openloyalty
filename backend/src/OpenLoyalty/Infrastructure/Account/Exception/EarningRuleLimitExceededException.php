<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Infrastructure\Account\Exception;

/**
 * Class EarningRuleLimitExceededException.
 */
class EarningRuleLimitExceededException extends \Exception
{
    protected $message = 'Limit exceeded';
}
