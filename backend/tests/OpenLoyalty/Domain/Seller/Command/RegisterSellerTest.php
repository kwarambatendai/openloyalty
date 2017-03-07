<?php

namespace OpenLoyalty\Domain\Seller\Command;

use OpenLoyalty\Domain\Seller\Event\SellerWasRegistered;
use OpenLoyalty\Domain\Seller\PosId;
use OpenLoyalty\Domain\Seller\SellerId;

/**
 * Class RegisterSellerTest.
 */
class RegisterSellerTest extends SellerCommandHandlerTest
{
    /**
     * @test
     */
    public function it_registers_new_seller()
    {
        $sellerId = new SellerId('00000000-0000-0000-0000-000000000000');
        $data = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'open@loyalty.com',
            'phone' => '123456789',
            'posId' => new PosId('00000000-0000-0000-0000-000000000000'),
            'createdAt' => new \DateTime(),
        ];
        $this->scenario
            ->withAggregateId($sellerId)
            ->given([])
            ->when(new RegisterSeller($sellerId, $data))
            ->then(array(
                new SellerWasRegistered($sellerId, $data)
            ));
    }

    /**
     * @test
     * @expectedException \Assert\InvalidArgumentException
     */
    public function it_throws_error_on_empty_field()
    {
        $sellerId = new SellerId('00000000-0000-0000-0000-000000000000');
        $data = [
            'firstName' => 'John',
            'email' => 'open@loyalty.com',
            'phone' => '123456789',
            'posId' => new PosId('00000000-0000-0000-0000-000000000000'),
        ];
        $this->scenario
            ->withAggregateId($sellerId)
            ->given([])
            ->when(new RegisterSeller($sellerId, $data))
            ->then([]);
    }
}
