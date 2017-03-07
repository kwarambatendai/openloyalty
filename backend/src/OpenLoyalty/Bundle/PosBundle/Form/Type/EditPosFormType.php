<?php

namespace OpenLoyalty\Bundle\PosBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

/**
 * Class EditPosFormType.
 */
class EditPosFormType extends AbstractType
{
    public function getParent()
    {
        return CreatePosFormType::class;
    }
}
