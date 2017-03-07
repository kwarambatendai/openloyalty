<?php

namespace OpenLoyalty\Bundle\UserBundle\Service;

interface TokenGenerator
{
    /**
     * @return string
     */
    public function generateToken();
}
