<?php

namespace OpenLoyalty\Bundle\SegmentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

/**
 * Class EditSegmentFormType.
 */
class EditSegmentFormType extends AbstractType
{
    public function getParent()
    {
        return SegmentFormType::class;
    }
}
