<?php

namespace OpenLoyalty\Domain\Transaction\Command;

use OpenLoyalty\Domain\Transaction\CustomerId;
use OpenLoyalty\Domain\Transaction\Event\CustomerWasAssignedToTransaction;
use OpenLoyalty\Domain\Transaction\Event\TransactionWasRegistered;
use OpenLoyalty\Domain\Transaction\TransactionId;

/**
 * Class AssignCustomerToTransactionTest.
 */
class AssignCustomerToTransactionTest extends TransactionCommandHandlerTest
{
    /**
     * @test
     */
    public function it_assign_customer_to_transaction()
    {
        $transactionId = new TransactionId('00000000-0000-0000-0000-000000000000');
        $customerId = new CustomerId('00000000-0000-0000-0000-000000000011');

        $this->scenario
            ->withAggregateId($transactionId)
            ->given([
                new TransactionWasRegistered($transactionId, $this->getTransactionData(), $this->getCustomerData())
            ])
            ->when(new AssignCustomerToTransaction($transactionId, $customerId))
            ->then(array(
                new CustomerWasAssignedToTransaction(
                    $transactionId,
                    $customerId
                )
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
