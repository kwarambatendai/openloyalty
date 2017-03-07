<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\LevelBundle\Form\Type;

use Broadway\UuidGenerator\UuidGeneratorInterface;
use OpenLoyalty\Bundle\LevelBundle\Model\SpecialReward;
use OpenLoyalty\Domain\Level\SpecialRewardId;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class SpecialRewardFormType.
 */
class SpecialRewardFormType extends AbstractType
{
    /**
     * @var UuidGeneratorInterface
     */
    protected $uuidGenerator;

    /**
     * RewardFormType constructor.
     *
     * @param UuidGeneratorInterface $uuidGenerator
     */
    public function __construct(UuidGeneratorInterface $uuidGenerator)
    {
        $this->uuidGenerator = $uuidGenerator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('active', CheckboxType::class, [
            'required' => false,
        ]);
        $builder->add('startAt', DateType::class, [
            'required' => true,
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('endAt', DateType::class, [
            'required' => true,
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'OpenLoyalty\Bundle\LevelBundle\Model\SpecialReward',
            'empty_data' => function (FormInterface $form) {
                $emptyData = new SpecialReward();
                $emptyData->setSpecialRewardId(new SpecialRewardId($this->uuidGenerator->generate()));

                return $emptyData;
            },
        ]);
    }

    public function getParent()
    {
        return RewardFormType::class;
    }
}
