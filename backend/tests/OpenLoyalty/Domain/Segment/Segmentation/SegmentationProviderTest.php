<?php

namespace OpenLoyalty\Domain\Segment\Segmentation;

use Broadway\ReadModel\RepositoryInterface;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerTransactionsSummary;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerTransactionsSummaryRepository;
use OpenLoyalty\Domain\Model\SKU;
use OpenLoyalty\Domain\Segment\CriterionId;
use OpenLoyalty\Domain\Segment\Model\Criteria\AverageTransactionAmount;
use OpenLoyalty\Domain\Segment\Model\Criteria\BoughtInPos;
use OpenLoyalty\Domain\Segment\Model\Criteria\TransactionAmount;
use OpenLoyalty\Domain\Segment\Model\Criteria\TransactionCount;
use OpenLoyalty\Domain\Segment\Model\SegmentPart;
use OpenLoyalty\Domain\Segment\Segment;
use OpenLoyalty\Domain\Segment\Segmentation\CriteriaEvaluators\AverageTransactionAmountEvaluator;
use OpenLoyalty\Domain\Segment\Segmentation\CriteriaEvaluators\BoughtInPosEvaluator;
use OpenLoyalty\Domain\Segment\Segmentation\CriteriaEvaluators\CustomerValidator;
use OpenLoyalty\Domain\Segment\Segmentation\CriteriaEvaluators\TransactionAmountEvaluator;
use OpenLoyalty\Domain\Segment\Segmentation\CriteriaEvaluators\TransactionCountEvaluator;
use OpenLoyalty\Domain\Segment\SegmentId;
use OpenLoyalty\Domain\Segment\SegmentPartId;
use OpenLoyalty\Domain\Transaction\CustomerId;
use OpenLoyalty\Domain\Transaction\Model\Item;
use OpenLoyalty\Domain\Transaction\PosId;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetails;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetailsRepository;
use OpenLoyalty\Domain\Transaction\TransactionId;

/**
 * Class SegmentationProviderTest.
 */
class SegmentationProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RepositoryInterface
     */
    protected $transactionDetailsRepository;

    /**
     * @var CustomerTransactionsSummaryRepository
     */
    protected $customerDetailsRepository;

    /**
     * @var SegmentationProvider
     */
    protected $segmentationProvider;

    /**
     * @var CustomerId
     */
    protected $customer1;

    /**
     * @var CustomerId
     */
    protected $customer2;

    /**
     * @var CustomerValidator
     */
    protected $customerValidator;

    /**
     * @var CustomerId
     */
    protected $customer3;

    public function setUp()
    {
        $this->customer1 = new CustomerId('00000000-0000-0000-0000-000000000001');
        $this->customer2 = new CustomerId('00000000-0000-0000-0000-000000000002');
        $this->customer3 = new CustomerId('00000000-0000-0000-0000-000000000003');

        $transactions = [];

        $posTransaction = new TransactionDetails(new TransactionId('00000000-0000-0000-0000-000000000000'));
        $posTransaction->setPosId(new PosId('00000000-0000-0000-0000-000000000000'));
        $posTransaction->setCustomerId($this->customer1);
        $posTransaction->setItems([new Item(new SKU('123'), 'item1', 1, 10, 'test', 'test')]);
        $transactions[] = $posTransaction;

        $posTransaction2 = new TransactionDetails(new TransactionId('00000000-0000-0000-0000-000000000002'));
        $posTransaction2->setPosId(new PosId('00000000-0000-0000-0000-000000000001'));
        $posTransaction2->setCustomerId($this->customer2);
        $posTransaction2->setItems([new Item(new SKU('123'), 'item1', 1, 100, 'test', 'test')]);
        $transactions[] = $posTransaction2;

        $posTransaction3 = new TransactionDetails(new TransactionId('00000000-0000-0000-0000-000000000003'));
        $posTransaction3->setPosId(new PosId('00000000-0000-0000-0000-000000000000'));
        $posTransaction3->setCustomerId($this->customer3);
        $posTransaction3->setItems([new Item(new SKU('123'), 'item1', 1, 1, 'test', 'test')]);
        $transactions[] = $posTransaction3;

        $posTransaction4 = new TransactionDetails(new TransactionId('00000000-0000-0000-0000-000000000004'));
        $posTransaction4->setPosId(new PosId('00000000-0000-0000-0000-000000000000'));
        $posTransaction4->setCustomerId($this->customer3);
        $posTransaction4->setItems([new Item(new SKU('123'), 'item1', 1, 99, 'test', 'test')]);
        $transactions[] = $posTransaction4;

        $this->transactionDetailsRepository = $this->getMock(TransactionDetailsRepository::class);
        $this->transactionDetailsRepository->method('findBy')->with($this->arrayHasKey('posId'))->will(
            $this->returnCallback(function (array $arg) use ($transactions) {
                $posId = $arg['posId'];
                $ret = [];

                foreach ($transactions as $transaction) {
                    if ($transaction->getPosId()->__toString() == $posId) {
                        $ret[] = $transaction;
                    }
                }

                return $ret;
            })
        );
        $this->transactionDetailsRepository->method('findAll')->willReturn($transactions);
        $this->transactionDetailsRepository->method('findAllWithCustomer')->willReturn($transactions);

        $this->customerDetailsRepository = $this->getMock(CustomerDetailsRepository::class);
        $this->customerDetailsRepository->method('findAllWithAverageTransactionAmountBetween')
            ->with(
                $this->logicalOr($this->equalTo(40), $this->equalTo(0)),
                $this->logicalOr($this->equalTo(200), $this->equalTo(39.99))
            )->will($this->returnCallback(function ($a, $b) {
                if ($a == 40 && $b == 200) {
                    return [
                        new CustomerDetails(new \OpenLoyalty\Domain\Customer\CustomerId($this->customer2->__toString())),
                        new CustomerDetails(new \OpenLoyalty\Domain\Customer\CustomerId($this->customer3->__toString())),
                    ];
                }
                if ($a == 0 && $b = 39.99) {
                    return [
                        new CustomerDetails(new \OpenLoyalty\Domain\Customer\CustomerId($this->customer1->__toString())),
                    ];
                }

                return [];
            }));

        $this->customerDetailsRepository->method('findAllWithTransactionAmountBetween')
            ->with(
                $this->equalTo(0), $this->equalTo(40))->willReturn([
                new CustomerDetails(new \OpenLoyalty\Domain\Customer\CustomerId($this->customer1->__toString())),
                new CustomerDetails(new \OpenLoyalty\Domain\Customer\CustomerId($this->customer3->__toString())),
            ]);

        $this->customerDetailsRepository->method('findAllWithTransactionCountBetween')
            ->with(
                $this->logicalOr($this->equalTo(2), $this->equalTo(1)),
                $this->logicalOr($this->equalTo(10), $this->equalTo(1))
            )
            ->will($this->returnCallback(function ($a, $b) {
                if ($a == 2 && $b == 10) {
                    return [
                        new CustomerDetails(new \OpenLoyalty\Domain\Customer\CustomerId($this->customer3->__toString())),
                    ];
                }
                if ($a == 1 && $b == 1) {
                    return [
                        new CustomerDetails(new \OpenLoyalty\Domain\Customer\CustomerId($this->customer1->__toString())),
                        new CustomerDetails(new \OpenLoyalty\Domain\Customer\CustomerId($this->customer2->__toString())),
                    ];
                }

                return [];
            }));

        $this->customerValidator = $this->getMock(CustomerValidator::class);
        $this->customerValidator->method('isValid')->with($this->isInstanceOf(CustomerId::class))->willReturn(true);

        $this->segmentationProvider = new SegmentationProvider();
        $this->segmentationProvider->addEvaluator(new BoughtInPosEvaluator($this->transactionDetailsRepository, $this->customerValidator));
        $this->segmentationProvider->addEvaluator(new TransactionCountEvaluator($this->customerDetailsRepository));
        $this->segmentationProvider->addEvaluator(new AverageTransactionAmountEvaluator($this->customerDetailsRepository));
        $this->segmentationProvider->addEvaluator(new TransactionAmountEvaluator($this->customerDetailsRepository));
    }

    /**
     * @test
     */
    public function it_return_customers_who_bought_in_pos()
    {
        $segment = new Segment(new SegmentId('00000000-0000-0000-0000-000000000000'), 'test');
        $part = new SegmentPart(new SegmentPartId('00000000-0000-0000-0000-000000000001'));
        $criterion = new BoughtInPos(new CriterionId('00000000-0000-0000-0000-000000000002'));
        $criterion->setPosIds(['00000000-0000-0000-0000-000000000000']);
        $part->addCriterion($criterion);
        $segment->addPart($part);

        $customers = $this->segmentationProvider->evaluateSegment($segment);
        $this->assertEquals([
            $this->customer1->__toString() => $this->customer1->__toString(),
            $this->customer3->__toString() => $this->customer3->__toString(),
        ], $customers);
    }

    /**
     * @test
     */
    public function it_return_customers_with_at_least_two_transactions()
    {
        $segment = new Segment(new SegmentId('00000000-0000-0000-0000-000000000000'), 'test');
        $part = new SegmentPart(new SegmentPartId('00000000-0000-0000-0000-000000000001'));
        $criterion = new TransactionCount(new CriterionId('00000000-0000-0000-0000-000000000002'));
        $criterion->setMin(2);
        $criterion->setMax(10);
        $part->addCriterion($criterion);
        $segment->addPart($part);

        $customers = $this->segmentationProvider->evaluateSegment($segment);
        $this->assertEquals([
            $this->customer3->__toString() => $this->customer3->__toString(),
        ], $customers);
    }

    /**
     * @test
     */
    public function it_return_customers_with_only_one_transaction()
    {
        $segment = new Segment(new SegmentId('00000000-0000-0000-0000-000000000000'), 'test');
        $part = new SegmentPart(new SegmentPartId('00000000-0000-0000-0000-000000000001'));
        $criterion = new TransactionCount(new CriterionId('00000000-0000-0000-0000-000000000002'));
        $criterion->setMin(1);
        $criterion->setMax(1);
        $part->addCriterion($criterion);
        $segment->addPart($part);

        $customers = $this->segmentationProvider->evaluateSegment($segment);
        $this->assertEquals([
            $this->customer1->__toString() => $this->customer1->__toString(),
            $this->customer2->__toString() => $this->customer2->__toString(),
        ], $customers);
    }

    /**
     * @test
     */
    public function it_return_customers_with_average_transaction_amount_greater_than_40()
    {
        $segment = new Segment(new SegmentId('00000000-0000-0000-0000-000000000000'), 'test');
        $part = new SegmentPart(new SegmentPartId('00000000-0000-0000-0000-000000000001'));
        $criterion = new AverageTransactionAmount(new CriterionId('00000000-0000-0000-0000-000000000002'));
        $criterion->setFromAmount(40);
        $criterion->setToAmount(200);
        $part->addCriterion($criterion);
        $segment->addPart($part);

        $customers = $this->segmentationProvider->evaluateSegment($segment);
        $this->assertEquals([
            $this->customer2->__toString() => $this->customer2->__toString(),
            $this->customer3->__toString() => $this->customer3->__toString(),
        ], $customers);
    }

    /**
     * @test
     */
    public function it_return_customers_with_average_transaction_amount_lower_than_40()
    {
        $segment = new Segment(new SegmentId('00000000-0000-0000-0000-000000000000'), 'test');
        $part = new SegmentPart(new SegmentPartId('00000000-0000-0000-0000-000000000001'));
        $criterion = new AverageTransactionAmount(new CriterionId('00000000-0000-0000-0000-000000000002'));
        $criterion->setFromAmount(0);
        $criterion->setToAmount(39.99);
        $part->addCriterion($criterion);
        $segment->addPart($part);

        $customers = $this->segmentationProvider->evaluateSegment($segment);
        $this->assertEquals([
            $this->customer1->__toString() => $this->customer1->__toString(),
        ], $customers);
    }

    /**
     * @test
     */
    public function it_return_customers_with_transaction_amount_lower_than_40()
    {
        $segment = new Segment(new SegmentId('00000000-0000-0000-0000-000000000000'), 'test');
        $part = new SegmentPart(new SegmentPartId('00000000-0000-0000-0000-000000000001'));
        $criterion = new TransactionAmount(new CriterionId('00000000-0000-0000-0000-000000000002'));
        $criterion->setFromAmount(0);
        $criterion->setToAmount(40);
        $part->addCriterion($criterion);
        $segment->addPart($part);

        $customers = $this->segmentationProvider->evaluateSegment($segment);

        $this->assertEquals([
            $this->customer1->__toString() => $this->customer1->__toString(),
            $this->customer3->__toString() => $this->customer3->__toString(),
        ], $customers);
    }

    /**
     * @test
     */
    public function it_return_customers_with_average_transaction_amount_greater_than_40_or_only_one_transaction()
    {
        $segment = new Segment(new SegmentId('00000000-0000-0000-0000-000000000000'), 'test');
        $part = new SegmentPart(new SegmentPartId('00000000-0000-0000-0000-000000000001'));
        $criterion = new AverageTransactionAmount(new CriterionId('00000000-0000-0000-0000-000000000002'));
        $criterion->setFromAmount(40);
        $criterion->setToAmount(200);
        $part->addCriterion($criterion);
        $criterion2 = new TransactionCount(new CriterionId('00000000-0000-0000-0000-000000000003'));
        $criterion2->setMin(1);
        $criterion2->setMax(1);
        $part->addCriterion($criterion2);

        $segment->addPart($part);

        $customers = $this->segmentationProvider->evaluateSegment($segment);
        $this->assertEquals([
            $this->customer1->__toString() => $this->customer1->__toString(),
            $this->customer2->__toString() => $this->customer2->__toString(),
            $this->customer3->__toString() => $this->customer3->__toString(),
        ], $customers);
    }

    /**
     * @test
     */
    public function it_return_customers_with_average_transaction_amount_greater_than_40_and_only_one_transaction()
    {
        $segment = new Segment(new SegmentId('00000000-0000-0000-0000-000000000000'), 'test');
        $part = new SegmentPart(new SegmentPartId('00000000-0000-0000-0000-000000000001'));
        $criterion = new AverageTransactionAmount(new CriterionId('00000000-0000-0000-0000-000000000002'));
        $criterion->setFromAmount(40);
        $criterion->setToAmount(200);
        $part->addCriterion($criterion);

        $part2 = new SegmentPart(new SegmentPartId('00000000-0000-0000-0000-000000000002'));
        $criterion2 = new TransactionCount(new CriterionId('00000000-0000-0000-0000-000000000003'));
        $criterion2->setMin(1);
        $criterion2->setMax(1);
        $part2->addCriterion($criterion2);

        $segment->addPart($part);
        $segment->addPart($part2);

        $customers = $this->segmentationProvider->evaluateSegment($segment);
        $this->assertEquals([
            $this->customer2->__toString() => $this->customer2->__toString(),
        ], $customers);
    }
}
