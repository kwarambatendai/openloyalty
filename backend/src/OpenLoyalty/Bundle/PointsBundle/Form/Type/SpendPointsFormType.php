<?php

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
