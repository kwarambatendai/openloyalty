<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\UserBundle\Security\Authenticator;

use OpenLoyalty\Bundle\UserBundle\Service\AdminProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Class TokenAuthenticator.
 */
class TokenAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = array(
            'message' => 'Authentication Required',
        );

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request)
    {
        $token = $request->headers->get('X-AUTH-TOKEN');
        if (!$token) {
            $token = $request->query->get('auth_token');
        }
        if (!$token) {
            return;
        }

        // What you return here will be passed to getUser() as $credentials
        return array(
            'token' => $token,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $apiKey = $credentials['token'];

        if ($userProvider instanceof AdminProvider) {
            return $userProvider->loadUserByApiKey($apiKey);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = array(
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
        );

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe()
    {
        return false;
    }
}
