<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Seller\Event;

use OpenLoyalty\Domain\Seller\SellerId;

/**
 * Class SellerWasActivated.
 */
class SellerWasActivated extends SellerEvent
{
    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(new SellerId($data['sellerId']));
    }
}
