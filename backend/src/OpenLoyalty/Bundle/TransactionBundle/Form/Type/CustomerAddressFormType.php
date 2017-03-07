<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\TransactionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class CustomerAddressFormType.
 */
class CustomerAddressFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('street', TextType::class, [
            'label' => 'Street',
            'required' => false,
        ])->add('address1', TextType::class, [
            'label' => 'address1',
            'required' => false,
        ])->add('address2', TextType::class, [
            'label' => 'address2',
            'required' => false,
        ])->add('postal', TextType::class, [
            'label' => 'Post code',
            'required' => false,
        ])->add('city', TextType::class, [
            'label' => 'City',
            'required' => false,
        ])->add('province', TextType::class, [
            'label' => 'Province',
            'required' => false,
        ])->add('country', CountryType::class, [
            'label' => 'Country',
            'required' => false,
        ]);
    }
}
