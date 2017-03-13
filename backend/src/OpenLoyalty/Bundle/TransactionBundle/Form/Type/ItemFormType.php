<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\TransactionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ItemFormType.
 */
class ItemFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('sku', SKUFormType::class, [
            'required' => true,
            'constraints' => [new NotBlank()],
        ]);
        $builder->add('name', TextType::class, [
            'required' => true,
            'constraints' => [new NotBlank()],
        ]);
        $builder->add('quantity', IntegerType::class, [
            'required' => true,
            'constraints' => [new NotBlank()],
        ]);
        $builder->add('grossValue', NumberType::class, [
            'required' => true,
            'constraints' => [new NotBlank()],
        ]);
        $builder->add('category', TextType::class, [
            'required' => true,
            'constraints' => [new NotBlank()],
        ]);
        $builder->add('maker', TextType::class, [
            'required' => false,
        ]);

        $builder->add('labels', CollectionType::class, [
            'allow_add' => true,
            'allow_delete' => true,
            'entry_type' => LabelFormType::class,
        ]);
    }
}
