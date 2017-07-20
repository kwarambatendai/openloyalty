<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UserBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class SecurityController.
 */
class SecurityController extends FOSRestController
{
    /**
     * This method can be used to log out current user. It will revoke all refresh tokens assigned to current user so it will not be possible
     * to obtain new token based on stored refresh token.
     *
     * @return \FOS\RestBundle\View\View
     * @Route(name="oloy.security.revoke_refresh_token", path="/token/revoke")
     *
     * @Method("GET")
     * @Security("is_granted('REVOKE_REFRESH_TOKEN')")
     * @ApiDoc(
     *     name="Revoke all refresh tokens by logged in user",
     *     section="Security"
     * )
     */
    public function revokeRefreshTokenAction()
    {
        // find all tokens by logged user
        /** @var UserInterface $user */
        $user = $this->getUser();
        $tokenManager = $this->get('gesdinet.jwtrefreshtoken.refresh_token_manager');
        $tokenRepository = $this->getDoctrine()->getRepository('GesdinetJWTRefreshTokenBundle:RefreshToken');
        $tokens = $tokenRepository->findBy(['username' => $user->getUsername()]);
        foreach ($tokens as $token) {
            $tokenManager->delete($token);
        }

        return $this->view([], 200);
    }
}
