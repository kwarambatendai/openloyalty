<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\PosBundle\Model;

use OpenLoyalty\Domain\Pos\Pos as BasePos;

/**
 * Class Pos.
 */
class Pos extends BasePos
{
    public function __construct()
    {
    }

    public function toArray()
    {
        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'identifier' => $this->getIdentifier(),
            'location' => $this->getLocation() ? $this->getLocation()->serialize() : null,
        ];
    }
}
