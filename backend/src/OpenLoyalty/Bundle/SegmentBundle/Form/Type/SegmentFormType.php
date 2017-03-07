<?php

namespace OpenLoyalty\Bundle\SegmentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class SegmentFormType.
 */
class SegmentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
            'required' => true,
            'constraints' => [new NotBlank()],
        ]);
        $builder->add('active', CheckboxType::class, [
            'required' => false,
        ]);
        $builder->add('description', TextareaType::class, [
            'required' => false,
        ]);
        $builder->add('parts', CollectionType::class, [
            'required' => true,
            'allow_add' => true,
            'allow_delete' => true,
            'error_bubbling' => false,
            'constraints' => [new Count(['min' => 1])],
            'entry_type' => SegmentPartFormType::class,
        ]);
    }
}
