<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

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
