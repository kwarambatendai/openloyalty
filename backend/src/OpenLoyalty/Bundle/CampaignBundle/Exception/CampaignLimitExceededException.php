<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\CampaignBundle\Exception;

/**
 * Class CampaignLimitExceededException.
 */
class CampaignLimitExceededException extends CampaignLimitException
{
    protected $message = 'Limit exceeded';
}
