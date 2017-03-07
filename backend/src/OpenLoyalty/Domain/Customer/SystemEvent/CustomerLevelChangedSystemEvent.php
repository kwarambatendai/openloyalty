<?php

namespace OpenLoyalty\Domain\Customer\SystemEvent;

use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\LevelId;

/**
 * Class CustomerLevelChangedSystemEvent.
 */
class CustomerLevelChangedSystemEvent extends CustomerSystemEvent
{
    /**
     * @var LevelId
     */
    protected $levelId;

    public function __construct(CustomerId $customerId, LevelId $levelId)
    {
        parent::__construct($customerId);
        $this->levelId = $levelId;
    }

    /**
     * @return LevelId
     */
    public function getLevelId()
    {
        return $this->levelId;
    }
}
