<?php

namespace OpenLoyalty\Bundle\CampaignBundle\Exception;

/**
 * Class CampaignLimitExceededException.
 */
class CampaignLimitExceededException extends CampaignLimitException
{
    protected $message = 'Limit exceeded';
}
