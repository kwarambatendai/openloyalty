<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\UserBundle\Form\Type;

use OpenLoyalty\Bundle\UserBundle\Validator\Constraint\PasswordRequirements;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class CustomerSelfRegistrationFormType.
 */
class CustomerSelfRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('plainPassword', PasswordType::class, [
            'required' => true,
            'constraints' => [
                new NotBlank(),
                new PasswordRequirements([
                    'requireSpecialCharacter' => true,
                    'requireNumbers' => true,
                    'requireLetters' => true,
                    'requireCaseDiff' => true,
                    'minLength' => 8,
                ]),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }

    public function getParent()
    {
        return CustomerRegistrationFormType::class;
    }
}
