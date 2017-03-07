<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Pos\SystemEvent;

use OpenLoyalty\Domain\Pos\PosId;

/**
 * Class PosSystemEvent.
 */
abstract class PosSystemEvent
{
    /**
     * @var PosId
     */
    protected $posId;

    /**
     * PosSystemEvent constructor.
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
