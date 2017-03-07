<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\CampaignBundle\Exception;

/**
 * Class NoCouponsLeftException.
 */
class NoCouponsLeftException extends CampaignLimitException
{
    protected $message = 'No coupons left';
}
