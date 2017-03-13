<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class SellerEditFormType.
 */
class SellerEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('plainPassword');
        $builder->add('plainPassword', PasswordType::class, [
            'required' => false,
        ]);
    }

    public function getParent()
    {
        return SellerRegistrationFormType::class;
    }
}
