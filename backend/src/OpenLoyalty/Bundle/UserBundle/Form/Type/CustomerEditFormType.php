<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

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
