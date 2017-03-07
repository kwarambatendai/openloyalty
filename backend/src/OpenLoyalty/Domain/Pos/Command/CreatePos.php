<?php

namespace OpenLoyalty\Domain\Pos\Command;

use OpenLoyalty\Domain\Pos\Pos;
use OpenLoyalty\Domain\Pos\PosId;

/**
 * Class CreatePos.
 */
class CreatePos extends PosCommand
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
