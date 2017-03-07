<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Infrastructure\Seller\SystemEvent\Listener;

use OpenLoyalty\Domain\Pos\SystemEvent\PosUpdatedSystemEvent;
use OpenLoyalty\Domain\Seller\ReadModel\SellerDetails;
use OpenLoyalty\Domain\Seller\ReadModel\SellerDetailsRepository;

/**
 * Class UpdatePosDataListener.
 */
class UpdatePosDataListener
{
    /**
     * @var SellerDetailsRepository
     */
    protected $sellerDetailsRepository;

    /**
     * UpdatePosDataListener constructor.
     *
     * @param SellerDetailsRepository $sellerDetailsRepository
     */
    public function __construct(SellerDetailsRepository $sellerDetailsRepository)
    {
        $this->sellerDetailsRepository = $sellerDetailsRepository;
    }

    public function handlePosUpdated(PosUpdatedSystemEvent $event)
    {
        $sellers = $this->sellerDetailsRepository->findBy(['posId' => $event->getPosId()->__toString()]);

        /** @var SellerDetails $seller */
        foreach ($sellers as $seller) {
            $seller->setPosName($event->getPosName());
            $seller->setPosCity($event->getPosCity());
            $this->sellerDetailsRepository->save($seller);
        }
    }
}
