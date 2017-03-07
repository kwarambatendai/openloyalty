<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\UserBundle\Form\Handler;

use Broadway\CommandHandling\CommandBusInterface;
use Doctrine\ORM\EntityManager;
use OpenLoyalty\Bundle\UserBundle\Service\UserManager;
use OpenLoyalty\Domain\Seller\Command\UpdateSeller;
use OpenLoyalty\Domain\Seller\Exception\EmailAlreadyExistsException;
use OpenLoyalty\Domain\Seller\SellerId;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

/**
 * Class SellerEditFormHandler.
 */
class SellerEditFormHandler
{
    /**
     * @var CommandBusInterface
     */
    protected $commandBus;
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * SellerEditFormHandler constructor.
     *
     * @param CommandBusInterface $commandBus
     * @param UserManager         $userManager
     * @param EntityManager       $em
     */
    public function __construct(CommandBusInterface $commandBus, UserManager $userManager, EntityManager $em)
    {
        $this->commandBus = $commandBus;
        $this->userManager = $userManager;
        $this->em = $em;
    }

    public function onSuccess(SellerId $sellerId, FormInterface $form)
    {
        $sellerData = $form->getData();

        if (!empty($sellerData['email'])) {
            $email = $sellerData['email'];

            if ($this->isUserExistAndIsDifferentThanEdited($sellerId->__toString(), $email)) {
                $form->get('email')->addError(new FormError('This email is already taken'));

                return false;
            }
        }

        $command = new UpdateSeller($sellerId, $sellerData);

        try {
            $this->commandBus->dispatch($command);
        } catch (EmailAlreadyExistsException $e) {
            $form->get('email')->addError(new FormError($e->getMessage()));

            return false;
        }

        $user = $this->em->getRepository('OpenLoyaltyUserBundle:Seller')->find($sellerId->__toString());
        if (!empty($email)) {
            $user->setEmail($email);
        }
        if (!empty($sellerData['plainPassword'])) {
            $user->setPlainPassword($sellerData['plainPassword']);
        }
        $this->userManager->updateUser($user);

        return true;
    }

    private function isUserExistAndIsDifferentThanEdited($id, $email)
    {
        $qb = $this->em->createQueryBuilder()->select('u')->from('OpenLoyaltyUserBundle:Seller', 'u');
        $qb->andWhere('u.email = :email')->setParameter('email', $email);
        $qb->andWhere('u.id != :id')->setParameter('id', $id);

        $result = $qb->getQuery()->getResult();

        return count($result) > 0;
    }
}
