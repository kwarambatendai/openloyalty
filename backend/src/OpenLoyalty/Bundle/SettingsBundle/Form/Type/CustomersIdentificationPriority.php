<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\SettingsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class CustomersIdentificationPriority.
 */
class CustomersIdentificationPriority extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('priority', NumberType::class, [
            'constraints' => [new NotBlank()],
        ]);
        $builder->add('field', ChoiceType::class, [
            'constraints' => [new NotBlank()],
            'choices' => [
                'loyaltyCardNumber' => 'loyaltyCardNumber',
                'email' => 'email',
            ],
        ]);
    }
}
