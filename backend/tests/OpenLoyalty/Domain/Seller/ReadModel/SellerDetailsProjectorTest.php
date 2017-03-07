<?php

namespace OpenLoyalty\Domain\Seller\ReadModel;

use Broadway\ReadModel\InMemory\InMemoryRepository;
use Broadway\ReadModel\Projector;
use Broadway\ReadModel\Testing\ProjectorScenarioTestCase;
use OpenLoyalty\Domain\Pos\PosRepository;
use OpenLoyalty\Domain\Seller\Event\SellerWasActivated;
use OpenLoyalty\Domain\Seller\Event\SellerWasDeactivated;
use OpenLoyalty\Domain\Seller\Event\SellerWasDeleted;
use OpenLoyalty\Domain\Seller\Event\SellerWasRegistered;
use OpenLoyalty\Domain\Seller\Event\SellerWasUpdated;
use OpenLoyalty\Domain\Seller\PosId;
use OpenLoyalty\Domain\Seller\SellerId;

/**
 * Class SellerDetailsProjectorTest.
 */
class SellerDetailsProjectorTest extends ProjectorScenarioTestCase
{
    /**
     * @return Projector
     */
    protected function createProjector(InMemoryRepository $repository)
    {
        $posRepo = $this->getMock(PosRepository::class);
        $posRepo->method('findBy')->willReturn(null);
        return new SellerDetailsProjector($repository, $posRepo);
    }

    /**
     * @test
     */
    public function it_creates_a_read_model_on_register()
    {
        $sellerId = new SellerId('00000000-0000-0000-0000-000000000000');
        $data = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'open@loyalty.com',
            'phone' => '123456789',
            'posId' => (new PosId('00000000-0000-0000-0000-000000000000'))->__toString(),
            'createdAt' => new \DateTime(),
            'sellerId' => $sellerId->__toString(),
        ];

        $expectedReadModel = SellerDetails::deserialize($data);
        $this->scenario->given(array())
            ->when(new SellerWasRegistered($sellerId, $data))
            ->then(array(
                $expectedReadModel,
            ));
    }

    /**
     * @test
     */
    public function it_activates_seller()
    {
        $sellerId = new SellerId('00000000-0000-0000-0000-000000000000');
        $data = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'open@loyalty.com',
            'phone' => '123456789',
            'posId' => (new PosId('00000000-0000-0000-0000-000000000000'))->__toString(),
            'createdAt' => new \DateTime(),
            'sellerId' => $sellerId->__toString(),
        ];

        /** @var SellerDetails $expectedReadModel */
        $expectedReadModel = SellerDetails::deserialize($data);
        $expectedReadModel->setActive(true);
        $this->scenario
            ->given(array(
                new SellerWasRegistered($sellerId, $data),
            ))
            ->when(new SellerWasActivated($sellerId))
            ->then(array(
                $expectedReadModel,
            ));
    }

    /**
     * @test
     */
    public function it_deactivates_seller()
    {
        $sellerId = new SellerId('00000000-0000-0000-0000-000000000000');
        $data = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'open@loyalty.com',
            'phone' => '123456789',
            'posId' => (new PosId('00000000-0000-0000-0000-000000000000'))->__toString(),
            'createdAt' => new \DateTime(),
            'sellerId' => $sellerId->__toString(),
        ];

        /** @var SellerDetails $expectedReadModel */
        $expectedReadModel = SellerDetails::deserialize($data);
        $expectedReadModel->setActive(false);
        $this->scenario
            ->given(array(
                new SellerWasRegistered($sellerId, $data),
                new SellerWasActivated($sellerId)
            ))
            ->when(new SellerWasDeactivated($sellerId))
            ->then(array(
                $expectedReadModel,
            ));
    }

    /**
     * @test
     */
    public function it_deletes_seller()
    {
        $sellerId = new SellerId('00000000-0000-0000-0000-000000000000');
        $data = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'open@loyalty.com',
            'phone' => '123456789',
            'posId' => (new PosId('00000000-0000-0000-0000-000000000000'))->__toString(),
            'createdAt' => new \DateTime(),
            'sellerId' => $sellerId->__toString(),
        ];

        /** @var SellerDetails $expectedReadModel */
        $expectedReadModel = SellerDetails::deserialize($data);
        $expectedReadModel->setDeleted(true);
        $this->scenario
            ->given(array(
                new SellerWasRegistered($sellerId, $data),
            ))
            ->when(new SellerWasDeleted($sellerId))
            ->then(array(
                $expectedReadModel,
            ));
    }

    /**
     * @test
     */
    public function it_updates_seller()
    {
        $sellerId = new SellerId('00000000-0000-0000-0000-000000000000');
        $data = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'open@loyalty.com',
            'phone' => '123456789',
            'posId' => (new PosId('00000000-0000-0000-0000-000000000000'))->__toString(),
            'createdAt' => new \DateTime(),
            'sellerId' => $sellerId->__toString(),
        ];

        /** @var SellerDetails $expectedReadModel */
        $expectedReadModel = SellerDetails::deserialize($data);
        $expectedReadModel->setLastName('Kowalski');
        $this->scenario
            ->given(array(
                new SellerWasRegistered($sellerId, $data),
            ))
            ->when(new SellerWasUpdated($sellerId, ['lastName' => 'Kowalski']))
            ->then(array(
                $expectedReadModel,
            ));
    }
}
