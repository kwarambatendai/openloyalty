<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UserBundle\Form\Type;

use OpenLoyalty\Bundle\UserBundle\Entity\Admin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class AdminFormType.
 */
class AdminFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('plainPassword', TextType::class, [
            'required' => false,
        ]);
        $builder->add('external', CheckboxType::class, [
            'required' => false,
        ]);
        $builder->add('apiKey', TextType::class, [
            'required' => false,
        ]);

        $builder->add('isActive', CheckboxType::class, [
            'required' => false,
        ]);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            if (!$data instanceof Admin) {
                return;
            }
            if ($data->isExternal()) {
                $event->getForm()->remove('plainPassword');
                $data->setPlainPassword('');
            } else {
                $event->getForm()->remove('apiKey');
                $data->setApiKey(null);
            }
            $event->setData($data);
        });
    }

    public function getParent()
    {
        return AdminSelfEditFormType::class;
    }
}
