<?php

namespace OpenLoyalty\Bundle\CampaignBundle\Form\Type;

use OpenLoyalty\Bundle\CampaignBundle\Model\CampaignActivity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CampaignActivityFormType.
 */
class CampaignActivityFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('allTimeActive', CheckboxType::class, [
            'required' => false,
        ]);
        $builder->add('activeFrom', DateTimeType::class, [
            'required' => false,
            'widget' => 'single_text',
            'format' => DateTimeType::HTML5_FORMAT,
        ]);
        $builder->add('activeTo', DateTimeType::class, [
            'required' => false,
            'widget' => 'single_text',
            'format' => DateTimeType::HTML5_FORMAT,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CampaignActivity::class,
        ]);
    }
}
