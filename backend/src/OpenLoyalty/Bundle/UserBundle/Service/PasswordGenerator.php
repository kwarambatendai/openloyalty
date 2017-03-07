<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\UserBundle\Service;

interface PasswordGenerator
{
    public function generate($length = 10);
}
