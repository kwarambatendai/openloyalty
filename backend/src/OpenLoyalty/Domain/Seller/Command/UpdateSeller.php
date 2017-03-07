<?php

namespace OpenLoyalty\Domain\Seller\Command;

use OpenLoyalty\Domain\Seller\SellerId;

/**
 * Class UpdateSeller.
 */
class UpdateSeller extends SellerCommand
{
    protected $sellerData;

    /**
     * RegisterCustomerCommand.
     *
     * @param SellerId $sellerId
     * @param $sellerData
     */
    public function __construct(SellerId $sellerId, $sellerData)
    {
        parent::__construct($sellerId);

        $this->sellerData = $sellerData;
    }

    /**
     * @return mixed
     */
    public function getSellerData()
    {
        return $this->sellerData;
    }
}
