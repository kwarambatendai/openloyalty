<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UserBundle\Service;

/**
 * Class RandomNumberTokenGenerator.
 */
class RandomNumberTokenGenerator implements TokenGenerator
{
    public function generateToken()
    {
        return rtrim(strtr(base64_encode($this->getRandomNumber()), '+/', '-_'), '=');
    }
    private function getRandomNumber()
    {
        return hash('sha256', uniqid(mt_rand(), true), true);
    }
}
