<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Seller\Event;

use OpenLoyalty\Domain\Seller\PosId;
use OpenLoyalty\Domain\Seller\SellerId;

/**
 * Class SellerWasRegistered.
 */
class SellerWasRegistered extends SellerEvent
{
    protected $sellerData;

    public function __construct(SellerId $sellerId, array $sellerData)
    {
        parent::__construct($sellerId);
        $data = $sellerData;
        if (isset($data['createdAt']) && is_numeric($data['createdAt'])) {
            $tmp = new \DateTime();
            $tmp->setTimestamp($data['createdAt']);
            $data['createdAt'] = $tmp;
        }
        if (isset($data['posId']) && !$data['posId'] instanceof PosId) {
            $data['posId'] = new PosId($data['posId']);
        }

        $this->sellerData = $data;
    }

    public function serialize()
    {
        $data = $this->sellerData;

        if (isset($data['createdAt']) && $data['createdAt'] instanceof \DateTime) {
            $data['createdAt'] = $data['createdAt']->getTimestamp();
        }

        if ($data['posId'] instanceof PosId) {
            $data['posId'] = (string) $data['posId'];
        }

        return array_merge(parent::serialize(), array(
            'customerData' => $data,
        ));
    }

    public static function deserialize(array $data)
    {
        if (isset($data['createdAt']) && is_numeric($data['createdAt'])) {
            $tmp = new \DateTime();
            $tmp->setTimestamp($data['createdAt']);
            $data['createdAt'] = $tmp;
        }

        return new self(
            new SellerId($data['sellerId']),
            $data['customerData']
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
