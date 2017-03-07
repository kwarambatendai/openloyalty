<?php

namespace OpenLoyalty\Bundle\UserBundle\Service;

interface PasswordGenerator
{
    public function generate($length = 10);
}
