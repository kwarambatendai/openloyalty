<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Account\Event;

use OpenLoyalty\Domain\Account\AccountId;
use OpenLoyalty\Domain\Account\Model\SpendPointsTransfer;

/**
 * Class PointsWereSpent.
 */
class PointsWereSpent extends AccountEvent
{
    /**
     * @var SpendPointsTransfer
     */
    protected $pointsTransfer;

    public function __construct(AccountId $accountId, SpendPointsTransfer $pointsTransfer)
    {
        parent::__construct($accountId);
        $this->pointsTransfer = $pointsTransfer;
    }

    /**
     * @return SpendPointsTransfer
     */
    public function getPointsTransfer()
    {
        return $this->pointsTransfer;
    }

    public function serialize()
    {
        return array_merge(
            parent::serialize(),
            [
                'pointsTransfer' => $this->pointsTransfer->serialize(),
            ]
        );
    }

    public static function deserialize(array $data)
    {
        return new self(new AccountId($data['accountId']), SpendPointsTransfer::deserialize($data['pointsTransfer']));
    }
}
