<?php

namespace OpenLoyalty\Bundle\CampaignBundle\Exception;

/**
 * Class NoCouponsLeftException.
 */
class NoCouponsLeftException extends CampaignLimitException
{
    protected $message = 'No coupons left';
}
