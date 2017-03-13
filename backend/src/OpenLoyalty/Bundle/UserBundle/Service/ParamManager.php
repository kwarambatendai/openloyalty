<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UserBundle\Service;

interface ParamManager
{
    public function stripNulls(array $params, $toLower = true, $escape = true, array $types = []);
}
