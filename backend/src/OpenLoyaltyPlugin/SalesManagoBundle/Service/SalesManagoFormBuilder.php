<?php

namespace OpenLoyaltyPlugin\SalesManagoBundle\Service;

use OpenLoyaltyPlugin\SalesManagoBundle\Form\Type\SalesManagoFormType;
use Symfony\Component\Form\FormFactory;

/**
 * Class SalesManagoFormBuilder.
 */
class SalesManagoFormBuilder
{
    /**
     * @var FormFactory
     */
    protected $formFactory;
    /**
     * @var
     */
    protected $em;

    /**
     * SalesManagoFormBuilder constructor.
     *
     * @param FormFactory $formFactory
     */
    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createForm()
    {
        $form = $this->formFactory->createNamed('salesManago', SalesManagoFormType::class);

        return $form;
    }
}
