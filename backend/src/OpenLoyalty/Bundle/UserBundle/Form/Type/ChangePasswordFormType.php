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
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ChangePasswordFormType.
 */
class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('currentPassword', PasswordType::class, array(
            'label' => 'Current password',
            'mapped' => false,
            'constraints' => new UserPassword(),
            'required' => true,
        ));
        $builder->add('plainPassword', PasswordType::class, array(
            'label' => 'New password',
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
        ));
    }
}
