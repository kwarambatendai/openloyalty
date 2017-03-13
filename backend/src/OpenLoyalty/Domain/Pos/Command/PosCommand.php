<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Pos\Command;

use OpenLoyalty\Domain\Pos\PosId;

/**
 * Class PosCommand.
 */
class PosCommand
{
    /**
     * @var PosId
     */
    protected $posId;

    /**
     * PosCommand constructor.
     *
     * @param PosId $posId
     */
    public function __construct(PosId $posId)
    {
        $this->posId = $posId;
    }

    /**
     * @return PosId
     */
    public function getPosId()
    {
        return $this->posId;
    }
}
