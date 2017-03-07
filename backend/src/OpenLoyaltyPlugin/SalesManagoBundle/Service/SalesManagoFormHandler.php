<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyaltyPlugin\SalesManagoBundle\Service;

use Doctrine\ORM\EntityManager;
use OpenLoyaltyPlugin\SalesManagoBundle\Entity\Config;
use Symfony\Component\Form\Form;

/**
 * Class SalesManagoFormBuilder.
 */
class SalesManagoFormHandler
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * SalesManagoFormHandler constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Form $form
     */
    public function handleForm($form)
    {
        $formData = $form->getData();
        /** @var Config $entity */
        $entity = $this->em->getRepository('SalesManagoBundle:Config')->findAll();

        if ($entity) {
            $entity = $entity[0];
            $entity->setSalesManagoApiEndpoint($formData->getSalesManagoApiEndpoint());
            $entity->setSalesManagoApiKey($formData->getSalesManagoApiKey());
            $entity->setSalesManagoApiSecret($formData->getSalesManagoApiSecret());
            $entity->setSalesManagoCustomerId($formData->getSalesManagoCustomerId());
            $entity->setSalesManagoIsActive($formData->getSalesManagoIsActive());
            $entity->setSalesManagoOwnerEmail($formData->getSalesManagoOwnerEmail());
            $this->em->persist($entity);
            $this->em->flush();
        } else {
            $this->em->persist($formData);
            $this->em->flush();
        }

        return;
    }
}
