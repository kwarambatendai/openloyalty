<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Customer\Event;

use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\LevelId;

/**
 * Class CustomerWasMovedToLevel.
 */
class CustomerWasMovedToLevel extends CustomerEvent
{
    /**
     * @var LevelId
     */
    protected $levelId;

    /**
     * @var \DateTime
     */
    protected $updateAt;

    /**
     * @var bool
     */
    protected $manually = false;

    public function __construct(CustomerId $customerId, LevelId $levelId = null, $manually = false)
    {
        parent::__construct($customerId);
        $this->levelId = $levelId;
        $this->updateAt = new \DateTime();
        $this->manually = $manually;
    }

    public function serialize()
    {
        return array_merge(parent::serialize(), [
           'levelId' => $this->levelId ? $this->levelId->__toString() : null,
            'updatedAt' => $this->updateAt ? $this->updateAt->getTimestamp() : null,
            'manually' => $this->manually,
        ]);
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        $event = new self(new CustomerId($data['customerId']), $data['levelId'] ? new LevelId($data['levelId']) : null, $data['manually']);
        if (isset($data['updatedAt'])) {
            $date = new \DateTime();
            $date->setTimestamp($data['updatedAt']);
            $event->setUpdateAt($date);
        }

        return $event;
    }

    /**
     * @return LevelId
     */
    public function getLevelId()
    {
        return $this->levelId;
    }

    /**
     * @return \DateTime
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }

    /**
     * @param \DateTime $updateAt
     */
    public function setUpdateAt($updateAt)
    {
        $this->updateAt = $updateAt;
    }

    /**
     * @return bool
     */
    public function isManually()
    {
        return $this->manually;
    }
}
