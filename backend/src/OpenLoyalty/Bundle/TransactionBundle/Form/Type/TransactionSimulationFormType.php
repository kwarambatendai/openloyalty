<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\TransactionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class TransactionSimulationFormType.
 */
class TransactionSimulationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('items', CollectionType::class, [
            'entry_type' => ItemFormType::class,
            'allow_delete' => true,
            'allow_add' => true,
            'error_bubbling' => false,
        ]);
        $builder->add('purchaseDate', DateType::class, [
            'required' => true,
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
    }
}
