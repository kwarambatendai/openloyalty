<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\SegmentBundle\Form\Type;

use Broadway\UuidGenerator\UuidGeneratorInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Count;

/**
 * Class SegmentPartFormType.
 */
class SegmentPartFormType extends AbstractType
{
    /**
     * @var UuidGeneratorInterface
     */
    protected $uuidGenerator;

    /**
     * SegmentPartFormType constructor.
     *
     * @param UuidGeneratorInterface $uuidGenerator
     */
    public function __construct(UuidGeneratorInterface $uuidGenerator)
    {
        $this->uuidGenerator = $uuidGenerator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('criteria', CollectionType::class, [
            'required' => true,
            'allow_add' => true,
            'allow_delete' => true,
            'constraints' => [new Count(['min' => 1])],
            'entry_type' => CriterionFormType::class,
            'error_bubbling' => false,
        ]);
        $builder->add('segmentPartId', HiddenType::class, [
            'empty_data' => function () {
                return $this->uuidGenerator->generate();
            },
        ]);
    }
}
