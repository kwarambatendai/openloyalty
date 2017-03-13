<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UserBundle\Form\Type;

use Broadway\ReadModel\RepositoryInterface;
use OpenLoyalty\Domain\Pos\Pos;
use OpenLoyalty\Domain\Pos\PosRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class SellerRegistrationFormType.
 */
class SellerRegistrationFormType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    protected $posRepository;

    /**
     * SellerRegistrationFormType constructor.
     *
     * @param PosRepository $posRepository
     */
    public function __construct(PosRepository $posRepository)
    {
        $this->posRepository = $posRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', TextType::class, [
            'label' => 'First name',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('lastName', TextType::class, [
            'label' => 'Last name',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('active', CheckboxType::class, [
            'required' => false,
        ]);
        $builder->add('email', EmailType::class, [
            'label' => 'Email',
            'required' => true,
            'constraints' => [
                new NotBlank(),
                new Email(),
            ],
        ]);
        $builder->add('phone', EmailType::class, [
            'label' => 'Phone',
            'required' => false,
        ]);

        $builder->add('phone', EmailType::class, [
            'label' => 'Phone',
            'required' => false,
        ]);

        $poses = array_map(function (Pos $pos) {
            return $pos->getPosId()->__toString();
        }, $this->posRepository->findAll());

        $builder->add('posId', ChoiceType::class, [
            'required' => true,
            'constraints' => [new NotBlank()],
            'choices' => array_combine($poses, $poses),
        ]);

        $builder->add('plainPassword', PasswordType::class, [
            'required' => true,
            'constraints' => [new NotBlank()],
        ]);
    }
}
