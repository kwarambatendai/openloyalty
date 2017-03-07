<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\UserBundle\Service;

/**
 * Class RandomStringPasswordGenerator.
 */
class RandomStringPasswordGenerator implements PasswordGenerator
{
    public function generate($length = 10)
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = [];
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < $length; ++$i) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }

        return implode($pass);
    }
}
