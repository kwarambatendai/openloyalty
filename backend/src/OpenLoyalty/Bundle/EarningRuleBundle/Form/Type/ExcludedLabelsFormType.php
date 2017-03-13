<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\EarningRuleBundle\Form\Type;

use OpenLoyalty\Bundle\EarningRuleBundle\Form\DataTransformer\LabelsDataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ExcludedLabelsFormType.
 */
class ExcludedLabelsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new LabelsDataTransformer());
    }

    public function getParent()
    {
        return TextType::class;
    }
}
