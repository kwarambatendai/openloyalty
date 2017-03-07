<?php

namespace OpenLoyalty\Domain\Transaction\Command;

use OpenLoyalty\Domain\Customer\Command\CustomerCommandHandlerTest;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Transaction\Event\TransactionWasRegistered;
use OpenLoyalty\Domain\Transaction\PosId;
use OpenLoyalty\Domain\Transaction\TransactionId;

/**
 * Class RegisterTransactionTest.
 */
class RegisterTransactionTest extends TransactionCommandHandlerTest
{
    /**
     * @test
     */
    public function it_registers_new_transaction()
    {
        $transactionId = new TransactionId('00000000-0000-0000-0000-000000000000');
        $transactionData = [
            'documentNumber' => '123',
            'purchasePlace' => 'wroclaw',
            'purchaseDate' => '1471859115',
            'documentType' => 'sell',
        ];
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

        $customerData = [
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

        $this->scenario
            ->withAggregateId($transactionId)
            ->given([])
            ->when(new RegisterTransaction($transactionId, $transactionData, $customerData, $items))
            ->then(array(
                new TransactionWasRegistered(
                    $transactionId,
                    $transactionData,
                    $customerData,
                    $items
                )
            ));
    }

    /**
     * @test
     */
    public function it_registers_transaction_with_pos()
    {
        $transactionId = new TransactionId('00000000-0000-0000-0000-000000000000');
        $posId = new PosId('00000000-0000-0000-0000-000000000011');

        $transactionData = [
            'documentNumber' => '123',
            'purchasePlace' => 'wroclaw',
            'purchaseDate' => '1471859115',
            'documentType' => 'sell',
        ];
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

        $customerData = [
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

        $this->scenario
            ->withAggregateId($transactionId)
            ->given([])
            ->when(new RegisterTransaction($transactionId, $transactionData, $customerData, $items, $posId))
            ->then(array(
                new TransactionWasRegistered(
                    $transactionId,
                    $transactionData,
                    $customerData,
                    $items,
                    $posId
                )
            ));
    }
}
