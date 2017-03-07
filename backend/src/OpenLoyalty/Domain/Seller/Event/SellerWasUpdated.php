<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Seller\Event;

use OpenLoyalty\Domain\Seller\SellerId;

/**
 * Class SellerWasUpdated.
 */
class SellerWasUpdated extends SellerEvent
{
    protected $sellerData;

    public function __construct(SellerId $sellerId, array $sellerData)
    {
        parent::__construct($sellerId);
        $this->sellerData = $sellerData;
    }

    public function serialize()
    {
        $data = $this->sellerData;

        return array_merge(parent::serialize(), array(
            'customerData' => $data,
        ));
    }

    public static function deserialize(array $data)
    {
        return new self(
            new SellerId($data['sellerId']),
            $data
        );
    }

    /**
     * @return array
     */
    public function getSellerData()
    {
        return $this->sellerData;
    }
}
