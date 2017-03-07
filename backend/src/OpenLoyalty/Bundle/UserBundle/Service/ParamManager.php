<?php

namespace OpenLoyalty\Bundle\UserBundle\Service;

interface ParamManager
{
    public function stripNulls(array $params, $toLower = true, $escape = true, array $types = []);
}
