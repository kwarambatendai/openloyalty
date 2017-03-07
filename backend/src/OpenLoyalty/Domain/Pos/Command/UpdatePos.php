<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Pos\Command;

use OpenLoyalty\Domain\Pos\Pos;
use OpenLoyalty\Domain\Pos\PosId;

/**
 * Class UpdatePos.
 */
class UpdatePos extends PosCommand
{
    /**
     * @var array
     */
    protected $posData = [];

    public function __construct(PosId $posId, array $posData)
    {
        parent::__construct($posId);
        Pos::validateRequiredData($posData);
        $this->posData = $posData;
    }

    /**
     * @return array
     */
    public function getPosData()
    {
        return $this->posData;
    }
}
