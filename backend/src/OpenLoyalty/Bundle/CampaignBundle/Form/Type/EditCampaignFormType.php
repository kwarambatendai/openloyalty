<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\CampaignBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

/**
 * Class EditCampaignFormType.
 */
class EditCampaignFormType extends AbstractType
{
    public function getParent()
    {
        return CampaignFormType::class;
    }
}
