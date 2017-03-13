<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\TransactionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class CustomerDetailsFormType.
 */
class CustomerDetailsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
            'required' => true,
            'constraints' => [new NotBlank()],
        ]);
        $builder->add('email', EmailType::class, [
            'required' => false,
        ]);
        $builder->add('phone', TextType::class, [
            'required' => false,
        ]);
        $builder->add('loyaltyCardNumber', TextType::class, [
            'required' => false,
        ]);
        $builder->add('nip', TextType::class);
        $builder->add('address', CustomerAddressFormType::class);
    }
}
