<?php

namespace OpenLoyalty\Bundle\CampaignBundle\Form\Type;

use OpenLoyalty\Bundle\CampaignBundle\Form\DataTransformer\CouponsDataTransformer;
use OpenLoyalty\Bundle\CampaignBundle\Form\DataTransformer\LevelsDataTransformer;
use OpenLoyalty\Bundle\CampaignBundle\Form\DataTransformer\SegmentsDataTransformer;
use OpenLoyalty\Bundle\CampaignBundle\Model\Campaign;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * Class CampaignFormType.
 */
class CampaignFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $rewardTypes = [
            Campaign::REWARD_TYPE_DISCOUNT_CODE,
            Campaign::REWARD_TYPE_EVENT_CODE,
            Campaign::REWARD_TYPE_FREE_DELIVERY_CODE,
            Campaign::REWARD_TYPE_GIFT_CODE,
            Campaign::REWARD_TYPE_VALUE_CODE,
        ];

        $builder->add('reward', ChoiceType::class, [
            'choices' => array_combine($rewardTypes, $rewardTypes),
            'required' => true,
            'constraints' => [new NotBlank()],
        ]);
        $builder->add('name', TextType::class, [
            'required' => true,
            'constraints' => [new NotBlank()],
        ]);
        $builder->add('shortDescription', TextareaType::class, [
            'required' => false,
        ]);
        $builder->add('conditionsDescription', TextareaType::class, [
            'required' => false,
        ]);
        $builder->add('usageInstruction', TextareaType::class, [
            'required' => false,
        ]);
        $builder->add('active', CheckboxType::class, [
            'required' => false,
        ]);
        $builder->add('costInPoints', IntegerType::class, [
            'required' => false,
            'constraints' => [new NotBlank()],
        ]);
        $builder->add('target', ChoiceType::class, [
            'required' => false,
            'choices' => [
                'level' => 'level',
                'segment' => 'segment',
            ],
            'mapped' => false,
        ]);
        $builder->add(
            $builder->create('levels', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'error_bubbling' => false,
            ])->addModelTransformer(new LevelsDataTransformer())
        );
        $builder->add(
            $builder->create('segments', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'error_bubbling' => false,
            ])->addModelTransformer(new SegmentsDataTransformer())
        );
        $builder->add('unlimited', CheckboxType::class, [
            'required' => false,
        ]);
        $builder->add('singleCoupon', CheckboxType::class, [
            'required' => false,
        ]);
        $builder->add('limit', IntegerType::class, [
            'required' => false,
        ]);
        $builder->add('limitPerUser', IntegerType::class, [
            'required' => false,
        ]);
        $builder->add(
            $builder->create('coupons', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'constraints' => [new Count(['min' => 1])],
                'error_bubbling' => false,
            ])->addModelTransformer(new CouponsDataTransformer())
        );

        $builder->add('campaignVisibility', CampaignVisibilityFormType::class, [
            'constraints' => [new Valid()],
        ]);
        $builder->add('campaignActivity', CampaignActivityFormType::class, [
            'constraints' => [new Valid()],
        ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            if (!isset($data['target'])) {
                return;
            }
            $target = $data['target'];
            if ($target == 'level') {
                $data['segments'] = [];
            } elseif ($target == 'segment') {
                $data['levels'] = [];
            }
            $event->setData($data);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Campaign::class,
        ]);
    }
}
