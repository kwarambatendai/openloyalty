<?php

namespace OpenLoyalty\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

/**
 * Class CustomerEditFormType.
 */
class CustomerEditFormType extends AbstractType
{
    public function getParent()
    {
        return CustomerRegistrationFormType::class;
    }
}
