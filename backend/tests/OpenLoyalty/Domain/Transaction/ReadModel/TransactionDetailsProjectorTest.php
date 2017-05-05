<?php

namespace OpenLoyalty\Domain\Transaction\ReadModel;

use Broadway\ReadModel\InMemory\InMemoryRepository;
use Broadway\ReadModel\Projector;
use Broadway\ReadModel\Testing\ProjectorScenarioTestCase;
use OpenLoyalty\Domain\Pos\PosRepository;
use OpenLoyalty\Domain\Transaction\CustomerId;
use OpenLoyalty\Domain\Transaction\Event\CustomerWasAssignedToTransaction;
use OpenLoyalty\Domain\Transaction\Event\TransactionWasRegistered;
use OpenLoyalty\Domain\Transaction\PosId;
use OpenLoyalty\Domain\Transaction\TransactionId;

/**
 * Class TransactionDetailsProjectorTest.
 */
class TransactionDetailsProjectorTest extends ProjectorScenarioTestCase
{
    /**
     * @param InMemoryRepository $repository
     *
     * @return Projector
     */
    protected function createProjector(InMemoryRepository $repository)
    {
        $posRepo = $this->getMockBuilder(PosRepository::class)->getMock();
        $posRepo->method('byId')->willReturn(null);

        return new TransactionDetailsProjector($repository, $posRepo);
    }

    /**
     * @test
     */
    public function it_created_read_model_when_new_transaction_registered()
    {
        $transactionId = new TransactionId('00000000-0000-0000-0000-000000000000');
        $posId = new PosId('00000000-0000-0000-0000-000000000011');

        $transactionData = $this->getTransactionData();
        $items = [
            [
                'sku' => ['code' => 'SKU1'],
                'name' => 'item 1',
                'quantity' => 1,
                'grossValue' => 1,
                'category' => 'aaa',
                'maker' => 'sss',
                'labels' => [
                    [
                        'key' => 'test',
                        'value' => 'label',
                    ],
                ],
            ],
            [
                'sku' => ['code' => 'SKU2'],
                'name' => 'item 2',
                'quantity' => 2,
                'grossValue' => 2,
                'category' => 'bbb',
                'maker' => 'ccc',
            ],
        ];

        $customerData = $this->getCustomerData();

        $expectedReadModel = TransactionDetails::deserialize(
            array_merge($transactionData, [
                'transactionId' => $transactionId->__toString(),
                'customerData' => $customerData,
                'items' => $items,
            ])
        );
        $expectedReadModel->setPosId($posId);

        $this->scenario->given([])
            ->when(new TransactionWasRegistered($transactionId, $transactionData, $customerData, $items, $posId))
            ->then([
                $expectedReadModel,
            ]);
    }

    /**
     * @test
     */
    public function it_updates_read_model_when_customer_was_assigned_to_transaction()
    {
        $transactionId = new TransactionId('00000000-0000-0000-0000-000000000000');
        $customerId = new CustomerId('00000000-0000-0000-0000-000000000011');

        $expectedReadModel = TransactionDetails::deserialize(
            array_merge([
                'transactionId' => $transactionId->__toString(),
                'customerData' => $this->getCustomerData(),
            ], $this->getTransactionData())
        );

        $expectedReadModel->setCustomerId($customerId);

        $this->scenario
            ->given([
                new TransactionWasRegistered($transactionId, $this->getTransactionData(), $this->getCustomerData()),
            ])
            ->when(new CustomerWasAssignedToTransaction($transactionId, $customerId))
            ->then(array(
                $expectedReadModel,
            ));
    }

    /**
     * @return array
     */
    protected function getTransactionData()
    {
        return [
            'documentNumber' => '123',
            'purchasePlace' => 'wroclaw',
            'purchaseDate' => '1471859115',
            'documentType' => 'sell',
        ];
    }

    /**
     * @return array
     */
    protected function getCustomerData()
    {
        return [
            'name' => 'Jan Nowak',
            'email' => 'ol@oy.com',
            'nip' => 'aaa',
            'phone' => '123',
            'loyaltyCardNumber' => '222',
            'address' => [
                'street' => 'Bagno',
                'address1' => '12',
                'city' => 'Warszawa',
                'country' => 'PL',
                'province' => 'Mazowieckie',
                'postal' => '00-800',
            ],
        ];
    }
}
