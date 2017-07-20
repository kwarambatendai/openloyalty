<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UserBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use OpenLoyalty\Bundle\UserBundle\Entity\Customer;
use OpenLoyalty\Bundle\UserBundle\Entity\User;
use OpenLoyalty\Bundle\UserBundle\Form\Type\ChangePasswordFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ChangePasswordController.
 *
 * @Security("is_granted('ROLE_USER')")
 */
class ChangePasswordController extends FOSRestController
{
    /**
     * Change logged user password.
     *
     * @param Request $request
     * @Route(name="oloy.user.change_password", path="/admin/password/change")
     * @Route(name="oloy.user.change_password_customer", path="/customer/password/change")
     * @Route(name="oloy.user.change_password_seller", path="/seller/password/change")
     * @Method("POST")
     * @ApiDoc(
     *     name="Change current password",
     *     section="Security",
     *     input={"name"="", "class"="OpenLoyalty\Bundle\UserBundle\Form\Type\ChangePasswordFormType"}
     * )
     * @Security("is_granted('PASSWORD_CHANGE')")
     *
     * @return \FOS\RestBundle\View\View
     */
    public function changeAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->get('form.factory')->createNamed('', ChangePasswordFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->get('oloy.user.user_manager');
            if ($user instanceof Customer) {
                $user->setTemporaryPasswordSetAt(null);
            }
            $manager->updateUser($user);

            return $this->view([
                'success' => true,
            ]);
        }

        return $this->view($form->getErrors(), 400);
    }
}
