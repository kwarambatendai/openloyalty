<?php

namespace OpenLoyalty\Bundle\EarningRuleBundle\Form\Type;

use OpenLoyalty\Bundle\EarningRuleBundle\Model\EarningRuleLimit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class EarningRuleLimitFormType.
 */
class EarningRuleLimitFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('active', CheckboxType::class, [
            'required' => false,
            'by_reference' => false,
        ]);
        $builder->add('period', ChoiceType::class, [
            'choices' => [
                EarningRuleLimit::PERIOD_DAY,
                EarningRuleLimit::PERIOD_WEEK,
                EarningRuleLimit::PERIOD_MONTH,
            ],
        ]);
        $builder->add('limit', NumberType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EarningRuleLimit::class,
        ]);
    }
}
