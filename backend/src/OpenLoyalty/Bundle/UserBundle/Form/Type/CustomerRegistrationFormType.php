<?php

namespace OpenLoyalty\Bundle\UserBundle\Form\Type;

use OpenLoyalty\Domain\Level\Level;
use OpenLoyalty\Domain\Level\LevelRepository;
use OpenLoyalty\Domain\Pos\Pos;
use OpenLoyalty\Domain\Pos\PosRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class CustomerRegistrationFormType.
 */
class CustomerRegistrationFormType extends AbstractType
{
    /**
     * @var LevelRepository
     */
    protected $levelRepository;

    /**
     * @var PosRepository
     */
    protected $posRepository;

    /**
     * CustomerRegistrationFormType constructor.
     *
     * @param LevelRepository $levelRepository
     * @param PosRepository   $posRepository
     */
    public function __construct(LevelRepository $levelRepository, PosRepository $posRepository)
    {
        $this->levelRepository = $levelRepository;
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
        $builder->add('gender', ChoiceType::class, [
            'label' => 'Gender',
            'required' => false,
            'choices' => [
                'male' => 'male',
                'female' => 'female',
            ],
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
        $builder->add('birthDate', DateType::class, [
            'label' => 'Birth date',
            'required' => false,
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd',
        ]);
        $builder->add('createdAt', DateTimeType::class, [
            'label' => 'Creation date',
            'required' => false,
            'widget' => 'single_text',
            'format' => DateTimeType::HTML5_FORMAT,
        ]);

        $this->addAddressFields($builder);
        $this->addCompanyFields($builder);

        $builder->add('loyaltyCardNumber', TextType::class, [
            'label' => 'Loyalty card number',
            'required' => false,
        ]);

        $builder->add('agreement1', CheckboxType::class, [
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('agreement2', CheckboxType::class, [
            'required' => false,
        ]);
        $builder->add('agreement3', CheckboxType::class, [
            'required' => false,
        ]);
        $builder->add('referral_customer_email', TextType::class, [
            'required' => false,
        ]);

        if ($options['includeLevelId'] && $this->levelRepository) {
            $posChoices = array_map(function (Level $level) {
                return $level->getLevelId()->__toString();
            }, $this->levelRepository->findAllActive());

            $builder->add('levelId', ChoiceType::class, [
                'required' => false,
                'choices' => array_combine($posChoices, $posChoices),
            ]);
        }

        if ($options['includePosId'] && $this->posRepository) {
            $posChoices = array_map(function (Pos $pos) {
                return $pos->getPosId()->__toString();
            }, $this->posRepository->findAll());

            $builder->add('posId', ChoiceType::class, [
                'required' => false,
                'choices' => array_combine($posChoices, $posChoices),
            ]);
        }
    }

    private function addCompanyFields(FormBuilderInterface $builder)
    {
        $company = $builder->create('company', FormType::class, [
            'label' => 'Company',
            'required' => false,
        ]);
        $company->add('name', TextType::class, [
            'label' => 'Name',
        ])->add('nip', TextType::class, [
            'label' => 'NIP',
        ]);
        $builder->add($company);
    }

    private function addAddressFields(FormBuilderInterface $builder)
    {
        $address = $builder->create('address', FormType::class, [
            'label' => 'Address',
            'required' => false,
        ]);

        $address->add('street', TextType::class, [
            'label' => 'Street',
            'required' => false,
        ])->add('address1', TextType::class, [
            'label' => 'address1',
            'required' => false,
        ])->add('address2', TextType::class, [
            'label' => 'address2',
            'required' => false,
        ])->add('postal', TextType::class, [
            'label' => 'Post code',
            'required' => false,
        ])->add('city', TextType::class, [
            'label' => 'City',
            'required' => false,
        ])->add('province', TextType::class, [
            'label' => 'Province',
            'required' => false,
        ])->add('country', CountryType::class, [
            'label' => 'Country',
            'required' => false,
        ]);

        $builder->add($address);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['includeLevelId' => false, 'includePosId' => false]);
    }
}
