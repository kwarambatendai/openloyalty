<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\EarningRuleBundle\Form\Type;

use OpenLoyalty\Bundle\EarningRuleBundle\Model\EarningRule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class EditEarningRuleFormType.
 */
class EditEarningRuleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $type = $options['type'];

        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [new NotBlank()],
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'constraints' => [new NotBlank()],
            ])
            ->add('active', CheckboxType::class, [
                'required' => false,
            ])
            ->add('allTimeActive', CheckboxType::class, [
                'required' => false,
            ])
            ->add('startAt', DateTimeType::class, [
                'required' => true,
                'widget' => 'single_text',
                'format' => DateTimeType::HTML5_FORMAT,
            ])
            ->add('endAt', DateTimeType::class, [
                'required' => true,
                'widget' => 'single_text',
                'format' => DateTimeType::HTML5_FORMAT,
            ]);
        if ($type == EarningRule::TYPE_POINTS) {
            $builder
                ->add('pointValue', IntegerType::class, [
                    'required' => true,
                    'constraints' => [new NotBlank()],
                ])
                ->add('excludedSKUs', ExcludedSKUsFormType::class)
                ->add('excludedLabels', ExcludedLabelsFormType::class)
                ->add('excludeDeliveryCost', CheckboxType::class, [
                    'required' => false,
                ])
                ->add('minOrderValue', NumberType::class);
        } elseif ($type == EarningRule::TYPE_EVENT) {
            $builder
                ->add('eventName', TextType::class, [
                    'required' => true,
                    'constraints' => [new NotBlank()],
                ])
                ->add('pointsAmount', IntegerType::class, [
                    'required' => true,
                    'constraints' => [new NotBlank()],
                ]);
        } elseif ($type == EarningRule::TYPE_CUSTOM_EVENT) {
            $builder
                ->add('eventName', TextType::class, [
                    'required' => true,
                    'constraints' => [new NotBlank()],
                ])
                ->add('pointsAmount', IntegerType::class, [
                    'required' => true,
                    'constraints' => [new NotBlank()],
                ])
                ->add('limit', EarningRuleLimitFormType::class);
        } elseif ($type == EarningRule::TYPE_PRODUCT_PURCHASE) {
            $builder->add(
                    'skuIds',
                    CollectionType::class,
                    [
                        'allow_add' => true,
                        'allow_delete' => true,
                        'entry_type' => TextType::class,
                        'constraints' => [new NotBlank(), new Count(['min' => 1])],
                    ]
                )->add(
                    'pointsAmount',
                    IntegerType::class,
                    [
                        'required' => true,
                        'constraints' => [new NotBlank()],
                    ]
                );
        } elseif ($type == EarningRule::TYPE_MULTIPLY_FOR_PRODUCT) {
            $builder
                ->add('skuIds', CollectionType::class, [
                    'allow_add' => true,
                    'allow_delete' => true,
                    'entry_type' => TextType::class,
                ])
                ->add('multiplier', NumberType::class, [
                    'required' => true,
                    'constraints' => [new NotBlank()],
                ])
                ->add('labels', LabelsFormType::class);
        } else {
            throw new InvalidArgumentException('Wrong "type" provided');
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'type',
        ]);

        $resolver->setDefaults([
            'data_class' => EarningRule::class,
        ]);
    }
}
