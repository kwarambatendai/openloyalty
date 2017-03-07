<?php

namespace OpenLoyalty\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class SellerEditFormType.
 */
class SellerEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('plainPassword');
        $builder->add('plainPassword', PasswordType::class, [
            'required' => false,
        ]);
    }

    public function getParent()
    {
        return SellerRegistrationFormType::class;
    }
}
