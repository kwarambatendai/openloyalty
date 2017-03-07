<?php

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
