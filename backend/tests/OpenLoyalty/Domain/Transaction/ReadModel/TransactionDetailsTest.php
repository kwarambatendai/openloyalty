<?php

namespace OpenLoyalty\Domain\Transaction\ReadModel;

use OpenLoyalty\Domain\Model\Label;
use OpenLoyalty\Domain\Model\SKU;
use OpenLoyalty\Domain\Transaction\Model\Item;
use OpenLoyalty\Domain\Transaction\TransactionId;

/**
 * Class TransactionDetailsTest.
 */
class TransactionDetailsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_correctly_calculates_values()
    {
        $transactionDetails = $this->getTransactionDetails();
        $this->assertEquals(110, $transactionDetails->getGrossValue());
        $this->assertEquals(110, $transactionDetails->getGrossValueWithoutDeliveryCosts());
    }

    /**
     * @test
     */
    public function it_correctly_calculates_values_and_take_delivery_costs_into_account()
    {
        $transactionDetails = $this->getTransactionDetails();
        $transactionDetails->setExcludedDeliverySKUs(['123']);
        $this->assertEquals(110, $transactionDetails->getGrossValue());
        $this->assertEquals(100, $transactionDetails->getGrossValueWithoutDeliveryCosts());
    }

    /**
     * @test
     */
    public function it_correctly_calculates_values_and_take_additional_excluded_skus_into_account()
    {
        $transactionDetails = $this->getTransactionDetails();
        $transactionDetails->setExcludedDeliverySKUs(['345']);
        $this->assertEquals(100, $transactionDetails->getGrossValue([new SKU('123')]));
        $this->assertEquals(100, $transactionDetails->getGrossValue(['123']));
        $this->assertEquals(0, $transactionDetails->getGrossValueWithoutDeliveryCosts([new SKU('123')]));
        $this->assertEquals(0, $transactionDetails->getGrossValueWithoutDeliveryCosts(['123']));
    }

    /**
     * @test
     */
    public function it_correctly_calculates_values_and_take_additional_excluded_labels_into_account()
    {
        $transactionDetails = $this->getTransactionDetails();
        $transactionDetails->setExcludedDeliverySKUs(['345']);
        $this->assertEquals(100, $transactionDetails->getGrossValue([], [new Label('1', '1')]));
        $this->assertEquals(0, $transactionDetails->getGrossValueWithoutDeliveryCosts([], [new Label('1', '1')]));
    }

    protected function getTransactionDetails()
    {
        $transactionId = new TransactionId('00000000-0000-0000-0000-000000000000');

        $transactionDetails = new TransactionDetails($transactionId);
        $item1 = new Item(new SKU('123'), 'test', 1, 10, 'test', 'test', [
            new Label('1', '1'),
        ]);
        $item2 = new Item(new SKU('345'), 'test', 1, 100, 'test', 'test', [
            new Label('2', '2'),
        ]);
        $transactionDetails->setItems([
            $item1,
            $item2,
        ]);

        return $transactionDetails;
    }
}
