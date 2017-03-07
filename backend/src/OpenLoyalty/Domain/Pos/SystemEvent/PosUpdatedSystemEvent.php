<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Pos\SystemEvent;

use OpenLoyalty\Domain\Pos\PosId;

/**
 * Class PosUpdatedSystemEvent.
 */
class PosUpdatedSystemEvent extends PosSystemEvent
{
    /**
     * @var string
     */
    protected $posName;

    /**
     * @var string
     */
    protected $posCity;

    public function __construct(PosId $posId, $name, $city = null)
    {
        parent::__construct($posId);
        $this->posName = $name;
        $this->posCity = $city;
    }

    /**
     * @return string
     */
    public function getPosName()
    {
        return $this->posName;
    }

    /**
     * @return string
     */
    public function getPosCity()
    {
        return $this->posCity;
    }
}
