<?php

namespace OpenLoyalty\Bundle\SegmentBundle\Form\Type;

use Broadway\UuidGenerator\UuidGeneratorInterface;
use OpenLoyalty\Domain\Pos\Pos;
use OpenLoyalty\Domain\Pos\PosRepository;
use OpenLoyalty\Domain\Segment\Model\Criteria\Anniversary;
use OpenLoyalty\Domain\Segment\Model\Criterion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Class CriterionFormType.
 */
class CriterionFormType extends AbstractType
{
    /**
     * @var PosRepository
     */
    protected $posRepository;

    /**
     * @var UuidGeneratorInterface
     */
    protected $uuidGenerator;

    /**
     * CriterionFormType constructor.
     *
     * @param PosRepository          $posRepository
     * @param UuidGeneratorInterface $uuidGenerator
     */
    public function __construct(PosRepository $posRepository, UuidGeneratorInterface $uuidGenerator)
    {
        $this->posRepository = $posRepository;
        $this->uuidGenerator = $uuidGenerator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = [
            Criterion::TYPE_BOUGHT_IN_POS,
            Criterion::TYPE_TRANSACTION_COUNT,
            Criterion::TYPE_AVERAGE_TRANSACTION_AMOUNT,
            Criterion::TYPE_TRANSACTION_PERCENT_IN_POS,
            Criterion::TYPE_PURCHASE_PERIOD,
            Criterion::TYPE_BOUGHT_LABELS,
            Criterion::TYPE_BOUGHT_MAKERS,
            Criterion::TYPE_ANNIVERSARY,
            Criterion::TYPE_LAST_PURCHASE_N_DAYS_BEFORE,
            Criterion::TYPE_BOUGHT_SKUS,
            Criterion::TYPE_TRANSACTION_AMOUNT,
        ];

        $pos = array_map(function (Pos $pos) {
            return $pos->getPosId()->__toString();
        }, $this->posRepository->findAll());

        $posChoices = array_combine($pos, $pos);

        $builder->add('type', ChoiceType::class, [
            'required' => true,
            'constraints' => [new NotBlank()],
            'choices' => array_combine($choices, $choices),
        ]);

        $builder->add('criterionId', HiddenType::class, [
            'empty_data' => function () {
                return $this->uuidGenerator->generate();
            },
        ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($posChoices) {
            $form = $event->getForm();
            $data = $event->getData();
            if (!isset($data['type'])) {
                $form->get('type')->addError(new FormError((new NotBlank())->message));

                return;
            }
            $type = $data['type'];
            switch ($type) {
                case Criterion::TYPE_BOUGHT_IN_POS:
                    $this->prepareBoughtInPosForm($form, $posChoices);
                    break;
                case Criterion::TYPE_TRANSACTION_COUNT:
                    $this->prepareTransactionCountForm($form);
                    break;
                case Criterion::TYPE_AVERAGE_TRANSACTION_AMOUNT:
                    $this->prepareAverageTransactionAmountForm($form);
                    break;
                case $type == Criterion::TYPE_PURCHASE_PERIOD:
                    $this->preparePurchasePeriodForm($form);
                    break;
                case Criterion::TYPE_LAST_PURCHASE_N_DAYS_BEFORE:
                    $this->prepareLastPurchaseNDaysBeforeForm($form);
                    break;
                case Criterion::TYPE_TRANSACTION_AMOUNT:
                    $this->prepareTransactionAmountForm($form);
                    break;
                case Criterion::TYPE_ANNIVERSARY:
                    $this->prepareAnniversaryForm($form);
                    break;
                case Criterion::TYPE_TRANSACTION_PERCENT_IN_POS:
                    $this->prepareTransactionPercentInPosForm($form, $posChoices);
                    break;
                case Criterion::TYPE_BOUGHT_SKUS:
                    $this->prepareBoughtSKUsForm($form);
                    break;
                case Criterion::TYPE_BOUGHT_MAKERS:
                    $this->prepareBoughtMakersForm($form);
                    break;
                case Criterion::TYPE_BOUGHT_LABELS:
                    $this->prepareBoughtLabelsForm($form);
                    break;
            }
        });
    }

    protected function prepareBoughtInPosForm(FormInterface $form, array $posChoices)
    {
        $form->add('posIds', CollectionType::class, [
            'allow_add' => true,
            'allow_delete' => true,
            'entry_type' => ChoiceType::class,
            'entry_options' => [
                'choices' => $posChoices,
            ],
            'error_bubbling' => false,
            'constraints' => [new Count(['min' => 1])],
        ]);
    }

    protected function prepareTransactionCountForm(FormInterface $form)
    {
        $form->add('min', IntegerType::class, [
            'required' => true,
            'constraints' => [new NotBlank(), new Range(['min' => 0])],
        ]);
        $form->add('max', IntegerType::class, [
            'required' => true,
            'constraints' => [new NotBlank(), new Range(['min' => 0])],
        ]);
    }

    protected function prepareAverageTransactionAmountForm(FormInterface $form)
    {
        $form->add('fromAmount', NumberType::class, [
            'required' => true,
            'constraints' => [new NotBlank(), new Range(['min' => 0])],
        ]);
        $form->add('toAmount', NumberType::class, [
            'required' => true,
            'constraints' => [new NotBlank(), new Range(['min' => 0])],
        ]);
    }

    protected function preparePurchasePeriodForm(FormInterface $form)
    {
        $form->add('fromDate', DateTimeType::class, [
            'required' => true,
            'widget' => 'single_text',
            'format' => DateTimeType::HTML5_FORMAT,
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $form->add('toDate', DateTimeType::class, [
            'required' => true,
            'widget' => 'single_text',
            'format' => DateTimeType::HTML5_FORMAT,
            'constraints' => [
                new NotBlank(),
            ],
        ]);
    }

    protected function prepareLastPurchaseNDaysBeforeForm(FormInterface $form)
    {
        $form->add('days', IntegerType::class, [
            'required' => true,
            'constraints' => [new NotBlank(), new Range(['min' => 1])],
        ]);
    }

    protected function prepareTransactionAmountForm(FormInterface $form)
    {
        $form->add('fromAmount', NumberType::class, [
            'required' => true,
            'constraints' => [new NotBlank(), new Range(['min' => 0])],
        ]);
        $form->add('toAmount', NumberType::class, [
            'required' => true,
            'constraints' => [new NotBlank(), new Range(['min' => 0])],
        ]);
    }

    protected function prepareAnniversaryForm(FormInterface $form)
    {
        $form->add('anniversaryType', ChoiceType::class, [
            'required' => true,
            'choices' => [
                Anniversary::TYPE_REGISTRATION => Anniversary::TYPE_REGISTRATION,
                Anniversary::TYPE_BIRTHDAY => Anniversary::TYPE_BIRTHDAY,
            ],
            'constraints' => [new NotBlank()],
        ]);
        $form->add('days', IntegerType::class, [
            'required' => true,
            'constraints' => [new NotBlank(), new Range(['min' => 1])],
        ]);
    }

    protected function prepareTransactionPercentInPosForm(FormInterface $form, array $posChoices)
    {
        $form->add('posId', ChoiceType::class, [
            'required' => true,
            'choices' => $posChoices,
            'constraints' => [new NotBlank()],
        ]);
        $form->add('percent', PercentType::class, [
            'required' => true,
            'constraints' => [new NotBlank()],
        ]);
    }

    protected function prepareBoughtSKUsForm(FormInterface $form)
    {
        $form->add('skuIds', CollectionType::class, [
            'allow_add' => true,
            'allow_delete' => true,
            'entry_type' => TextType::class,
            'error_bubbling' => false,
            'constraints' => [new Count(['min' => 1])],
        ]);
    }

    protected function prepareBoughtMakersForm(FormInterface $form)
    {
        $form->add('makers', CollectionType::class, [
            'allow_add' => true,
            'allow_delete' => true,
            'entry_type' => TextType::class,
            'error_bubbling' => false,
            'constraints' => [new Count(['min' => 1])],
        ]);
    }

    protected function prepareBoughtLabelsForm(FormInterface $form)
    {
        $form->add('labels', CollectionType::class, [
            'allow_add' => true,
            'allow_delete' => true,
            'entry_type' => LabelFormType::class,
            'error_bubbling' => false,
            'constraints' => [new Count(['min' => 1])],
        ]);
    }
}
