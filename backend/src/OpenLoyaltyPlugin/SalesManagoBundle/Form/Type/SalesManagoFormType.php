<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyaltyPlugin\SalesManagoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class SettingsFormType.
 */
class SalesManagoFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            $builder
                ->create('salesManagoIsActive', CheckboxType::class, [
                    'required' => false,
                ])
        );
        $builder->add(
            $builder
                ->create('salesManagoApiEndpoint', TextType::class, [
                    'constraints' => [new NotBlank()],
                ])
        );
        $builder->add(
            $builder
                ->create('salesManagoApiSecret', TextType::class, [
                    'constraints' => [new NotBlank()],
                ])
        );
        $builder->add(
            $builder
                ->create('salesManagoApiKey', TextType::class, [
                    'constraints' => [new NotBlank()],
                ])
        );
        $builder->add(
            $builder
                ->create('salesManagoCustomerId', TextType::class, [
                    'constraints' => [new NotBlank()],
                ])
        );
        $builder->add(
            $builder
                ->create('salesManagoOwnerEmail', TextType::class, [
                    'constraints' => [new NotBlank()],
                ])
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'OpenLoyaltyPlugin\SalesManagoBundle\Entity\Config',
        ]);
    }
}
