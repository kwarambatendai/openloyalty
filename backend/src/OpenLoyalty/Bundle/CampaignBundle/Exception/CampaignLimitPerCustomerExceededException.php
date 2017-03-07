<?php

namespace OpenLoyalty\Bundle\CampaignBundle\Exception;

/**
 * Class CampaignLimitPerCustomerExceededException.
 */
class CampaignLimitPerCustomerExceededException extends CampaignLimitException
{
    protected $message = 'Limit per customer exceeded';
}
