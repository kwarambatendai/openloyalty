<?php

namespace OpenLoyalty\Domain\Seller\Command;

use OpenLoyalty\Domain\Seller\Event\SellerWasRegistered;
use OpenLoyalty\Domain\Seller\Event\SellerWasUpdated;
use OpenLoyalty\Domain\Seller\PosId;
use OpenLoyalty\Domain\Seller\SellerId;

/**
 * Class UpdateSellerTest.
 */
class UpdateSellerTest extends SellerCommandHandlerTest
{
    /**
     * @test
     */
    public function it_registers_new_seller()
    {
        $sellerId = new SellerId('00000000-0000-0000-0000-000000000000');
        $rData = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'open@loyalty.com',
            'phone' => '123456789',
            'posId' => new PosId('00000000-0000-0000-0000-000000000000'),
            'createdAt' => new \DateTime(),
        ];
        $data = [
            'firstName' => 'John',
            'lastName' => 'Doe',
        ];
        $this->scenario
            ->withAggregateId($sellerId)
            ->given([
                new SellerWasRegistered($sellerId, $rData),
            ])
            ->when(new UpdateSeller($sellerId, $data))
            ->then(array(
                new SellerWasUpdated($sellerId, $data)
            ));
    }
}
