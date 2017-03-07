<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\PointsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

/**
 * Class SpendPointsFormType.
 */
class SpendPointsFormType extends AbstractType
{
    public function getParent()
    {
        return AddPointsFormType::class;
    }
}
