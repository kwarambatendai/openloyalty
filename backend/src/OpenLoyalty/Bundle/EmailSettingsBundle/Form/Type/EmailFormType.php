<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\EmailSettingsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class EmailFormType.
 */
class EmailFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            $builder->create(
                'key',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new Length(['max' => 255]),
                    ],
                ]
            )
        );

        $builder->add(
            $builder->create(
                'subject',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new Length(['max' => 255]),
                    ],
                ]
            )
        );

        $builder->add(
            $builder->create(
                'content',
                TextareaType::class,
                [
                    'constraints' => [new NotBlank()],
                ]
            )
        );
    }
}
