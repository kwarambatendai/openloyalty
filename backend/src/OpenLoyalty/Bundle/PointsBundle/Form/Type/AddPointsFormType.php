<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\PointsBundle\Form\Type;

use Broadway\ReadModel\RepositoryInterface;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Class AddPointsFormType.
 */
class AddPointsFormType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    protected $customerDetailsRepository;

    /**
     * AddPointsFormType constructor.
     *
     * @param RepositoryInterface $customerDetailsRepository
     */
    public function __construct(RepositoryInterface $customerDetailsRepository)
    {
        $this->customerDetailsRepository = $customerDetailsRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $customers = $this->customerDetailsRepository->findAll();
        $customerChoices = [];
        /** @var CustomerDetails $customer */
        foreach ($customers as $customer) {
            $customerChoices[$customer->getId()] = $customer->getId();
        }

        $builder->add('customer', ChoiceType::class, [
            'required' => true,
            'constraints' => [new NotBlank()],
            'choices' => $customerChoices,
        ]);

        $builder->add('points', IntegerType::class, [
            'attr' => ['min' => 1],
            'constraints' => [
                new NotBlank(),
                new Range(['min' => 1]),
            ],
        ]);

        $builder->add('comment', TextType::class, [
            'required' => false,
        ]);
    }
}
