<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Account\Event;

use OpenLoyalty\Domain\Account\AccountId;
use OpenLoyalty\Domain\Account\PointsTransferId;

/**
 * Class PointsTransferHasBeenExpired.
 */
class PointsTransferHasBeenExpired extends AccountEvent
{
    /**
     * @var PointsTransferId
     */
    protected $pointsTransferId;

    public function __construct(AccountId $accountId, PointsTransferId $pointsTransferId)
    {
        parent::__construct($accountId);
        $this->pointsTransferId = $pointsTransferId;
    }

    /**
     * @return PointsTransferId
     */
    public function getPointsTransferId()
    {
        return $this->pointsTransferId;
    }

    public function serialize()
    {
        return array_merge(
            parent::serialize(),
            [
                'pointsTransferId' => $this->pointsTransferId->__toString(),
            ]
        );
    }

    public static function deserialize(array $data)
    {
        return new self(new AccountId($data['accountId']), new PointsTransferId($data['pointsTransferId']));
    }
}
