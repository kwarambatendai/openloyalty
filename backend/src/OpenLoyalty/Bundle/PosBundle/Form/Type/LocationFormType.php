<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\PosBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class LocationFormType.
 */
class LocationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('street', TextType::class, [
            'label' => 'Street',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ])->add('address1', TextType::class, [
            'label' => 'address1',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ])->add('address2', TextType::class, [
            'label' => 'address2',
            'required' => false,
        ])->add('postal', TextType::class, [
            'label' => 'Post code',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ])->add('city', TextType::class, [
            'label' => 'City',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ])->add('province', TextType::class, [
            'label' => 'Province',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ])->add('country', CountryType::class, [
            'label' => 'Country',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ])->add('lat', TextType::class, [
            'required' => false,
        ])->add('long', TextType::class, [
            'required' => false,
        ]);
    }
}
