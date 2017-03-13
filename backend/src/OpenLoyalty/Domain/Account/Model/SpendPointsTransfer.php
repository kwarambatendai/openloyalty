<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Account\Model;

use OpenLoyalty\Domain\Account\PointsTransferId;

/**
 * Class SpendPointsTransfer.
 */
class SpendPointsTransfer extends PointsTransfer
{
    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        $createdAt = null;
        if (isset($data['createdAt'])) {
            $createdAt = new \DateTime();
            $createdAt->setTimestamp($data['createdAt']);
        }

        $transfer = new self(new PointsTransferId($data['id']), $data['value'], $createdAt, $data['canceled']);
        if (isset($data['comment'])) {
            $transfer->comment = $data['comment'];
        }
        if (isset($data['issuer'])) {
            $transfer->issuer = $data['issuer'];
        }

        return $transfer;
    }
}
