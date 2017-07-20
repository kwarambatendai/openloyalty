<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UserBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use OpenLoyalty\Bundle\UserBundle\Entity\User;
use OpenLoyalty\Bundle\UserBundle\Form\Type\PasswordResetFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ResetPasswordController.
 */
class ResetPasswordController extends FOSRestController
{
    /**
     * Purpose of this method is to provide "Forgot password" functionality.<br/>Invoking this method will send message tot he user with password reset url.
     *
     * @param Request $request
     * @Route(name="oloy.user.reset.request", path="/password/reset/request")
     * @Method("POST")
     * @ApiDoc(
     *     name="Request reset password",
     *     section="Security",
     *     parameters={{"name"="username", "required"=true, "dataType"="string"}},
     *     statusCodes={
     *       200="Returned when successful",
     *       400="Returned when username parameter is not present or resetting password already requested",
     *     }
     * )
     *
     * @return \FOS\RestBundle\View\View
     */
    public function resetRequestAction(Request $request)
    {
        $username = $request->request->get('username');
        if (!$username) {
            return $this->view(['error' => 'field "username" should not be empty'], 400);
        }
        $userManager = $this->get('oloy.user.user_manager');

        /** @var $user User */
        $user = $userManager->findUserByUsernameOrEmail($username);

        if (null === $user) {
            return $this->view(['success' => true]);
        }

        if ($user->isPasswordRequestNonExpired(86400)) {
            return $this->view(['error' => 'resetting password already requested'], 400);
        }

        if (null === $user->getConfirmationToken()) {
            $tokenGenerator = $this->get('oloy.user.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $this->get('oloy.user.email_provider')->resettingPasswordMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $userManager->updateUser($user);

        return $this->view(['success' => true]);
    }

    /**
     * Method allows to set new password after reset password requesting.
     *
     * @param Request $request
     *
     * @return \FOS\RestBundle\View\View
     * @Route(name="oloy.user.reset", path="/password/reset")
     * @Method("POST")
     * @ApiDoc(
     *     name="Reset password",
     *     section="Security",
     *     input={"class" = "OpenLoyalty\Bundle\UserBundle\Form\Type\PasswordResetFormType", "name" = "reset"},
     *     parameters={{"name"="token", "required"=true, "dataType"="string"}},
     *     statusCodes={
     *       200="Returned when successful",
     *       400="Returned when token parameter is not present or user with such token does not exist",
     *     }
     * )
     */
    public function resetAction(Request $request)
    {
        $userManager = $this->get('oloy.user.user_manager');
        $token = $request->get('token');
        if (!$token) {
            return $this->view(['error' => 'field "token" should not be empty'], 400);
        }
        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        $form = $this->get('form.factory')->createNamed('reset', PasswordResetFormType::class, $user, [
            'csrf_protection' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $user->setPasswordRequestedAt(null);
            $user->setConfirmationToken(null);
            $userManager->updateUser($user);

            return $this->view(['success' => true]);
        }

        return $this->view($form->getErrors(), 400);
    }
}
