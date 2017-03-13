<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Customer\Command;

use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\LevelId;

/**
 * Class MoveCustomerToLevel.
 */
class MoveCustomerToLevel extends CustomerCommand
{
    /**
     * @var LevelId
     */
    protected $levelId;

    protected $manually = false;

    public function __construct(CustomerId $customerId, LevelId $levelId = null, $manually = false)
    {
        parent::__construct($customerId);
        $this->levelId = $levelId;
        $this->manually = $manually;
    }

    /**
     * @return LevelId
     */
    public function getLevelId()
    {
        return $this->levelId;
    }

    /**
     * @return bool
     */
    public function isManually()
    {
        return $this->manually;
    }
}
