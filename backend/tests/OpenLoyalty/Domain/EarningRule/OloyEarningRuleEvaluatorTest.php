<?php


namespace OpenLoyalty\Domain\EarningRule;

use OpenLoyalty\Domain\Account\SystemEvent\AccountSystemEvents;
use OpenLoyalty\Domain\Account\TransactionId;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\ReadModel\InvitationDetailsRepository;
use OpenLoyalty\Domain\EarningRule\Algorithm\EarningRuleAlgorithmFactoryInterface;
use OpenLoyalty\Domain\EarningRule\Algorithm\MultiplyPointsForProductRuleAlgorithm;
use OpenLoyalty\Domain\EarningRule\Algorithm\PointsEarningRuleAlgorithm;
use OpenLoyalty\Domain\EarningRule\Algorithm\ProductPurchaseEarningRuleAlgorithm;
use OpenLoyalty\Domain\Model\Label;
use OpenLoyalty\Domain\Model\SKU;
use OpenLoyalty\Domain\Transaction\Model\Item;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetails;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetailsRepository;
use OpenLoyalty\Domain\Segment\ReadModel\SegmentedCustomersRepository;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;


/**
 * Class OloyEarningRuleEvaluatorTest
 */
class OloyEarningRuleEvaluatorTest extends \PHPUnit_Framework_TestCase
{

    const USER_ID = '00000000-0000-0000-0000-000000000000';

    /**
     * @test
     */
    public function it_returns_proper_value_for_given_transaction_and_points_earning_rule()
    {
        $pointsEarningRule = new PointsEarningRule(new EarningRuleId('00000000-0000-0000-0000-000000000000'));
        $pointsEarningRule->setPointValue(4);
        $pointsEarningRule->setExcludeDeliveryCost(false);

        $evaluator = $this->getEarningRuleEvaluator([$pointsEarningRule]);

        $points = $evaluator->evaluateTransaction(new TransactionId('00000000-0000-0000-0000-000000000000'), new CustomerId(static::USER_ID));
        $this->assertEquals(608, $points);
    }

    /**
     * @test
     */
    public function it_returns_proper_value_for_given_transaction_and_points_earning_rule_and_excluded_sku()
    {
        $pointsEarningRule = new PointsEarningRule(new EarningRuleId('00000000-0000-0000-0000-000000000000'));
        $pointsEarningRule->setPointValue(4);
        $pointsEarningRule->setExcludedSKUs([new SKU('000')]);
        $pointsEarningRule->setExcludeDeliveryCost(false);

        $evaluator = $this->getEarningRuleEvaluator([$pointsEarningRule]);

        $points = $evaluator->evaluateTransaction(new TransactionId('00000000-0000-0000-0000-000000000000'), new CustomerId(static::USER_ID));
        $this->assertEquals(208, $points);
    }

    /**
     * @test
     */
    public function it_returns_proper_value_for_given_transaction_and_points_earning_rule_and_excluded_label()
    {
        $pointsEarningRule = new PointsEarningRule(new EarningRuleId('00000000-0000-0000-0000-000000000000'));
        $pointsEarningRule->setPointValue(4);
        $pointsEarningRule->setExcludedLabels([new Label('color', 'red')]);
        $pointsEarningRule->setExcludeDeliveryCost(false);

        $evaluator = $this->getEarningRuleEvaluator([$pointsEarningRule]);

        $points = $evaluator->evaluateTransaction(new TransactionId('00000000-0000-0000-0000-000000000000'), new CustomerId(static::USER_ID));
        $this->assertEquals(560, $points);
    }

    /**
     * @test
     */
    public function it_returns_proper_value_for_given_transaction_and_points_earning_rule_without_delivery_costs()
    {
        $pointsEarningRule = new PointsEarningRule(new EarningRuleId('00000000-0000-0000-0000-000000000000'));
        $pointsEarningRule->setPointValue(4);
        $pointsEarningRule->setExcludedLabels([new Label('color', 'red')]);
        $pointsEarningRule->setExcludeDeliveryCost(true);

        $evaluator = $this->getEarningRuleEvaluator([$pointsEarningRule]);

        $points = $evaluator->evaluateTransaction(new TransactionId('00000000-0000-0000-0000-000000000000'), new CustomerId(static::USER_ID));
        $this->assertEquals(400, $points);
    }

    /**
     * @test
     */
    public function it_returns_proper_value_for_given_transaction_and_sku_rule()
    {
        $purchaseEarningRule = new ProductPurchaseEarningRule(
            new EarningRuleId('00000000-0000-0000-0000-000000000000')
        );
        $purchaseEarningRule->setSkuIds(['000']);
        $purchaseEarningRule->setPointsAmount(200);

        $evaluator = $this->getEarningRuleEvaluator([$purchaseEarningRule]);

        $points = $evaluator->evaluateTransaction(new TransactionId('00000000-0000-0000-0000-000000000000'), new CustomerId(static::USER_ID));
        $this->assertEquals(200, $points);
    }

    /**
     * @test
     */
    public function it_returns_proper_value_for_given_transaction_if_there_are_more_rules()
    {
        $pointsEarningRule = new PointsEarningRule(new EarningRuleId('00000000-0000-0000-0000-000000000000'));
        $pointsEarningRule->setPointValue(10);
        $pointsEarningRule->setExcludeDeliveryCost(false);

        $pointsEarningRule2 = new PointsEarningRule(new EarningRuleId('00000000-0000-0000-0000-000000000000'));
        $pointsEarningRule2->setPointValue(4);
        $pointsEarningRule2->setExcludeDeliveryCost(false);

        $evaluator = $this->getEarningRuleEvaluator([$pointsEarningRule, $pointsEarningRule2]);

        $points = $evaluator->evaluateTransaction(new TransactionId('00000000-0000-0000-0000-000000000000'), new CustomerId(static::USER_ID));
        $this->assertEquals(2128, $points);
    }

    /**
     * @test
     */
    public function it_returns_proper_value_for_given_transaction_if_there_are_more_rule_types()
    {
        $purchaseEarningRule = new ProductPurchaseEarningRule(
            new EarningRuleId('00000000-0000-0000-0000-000000000000')
        );
        $purchaseEarningRule->setSkuIds(['123']);
        $purchaseEarningRule->setPointsAmount(100);

        $pointsEarningRule2 = new PointsEarningRule(new EarningRuleId('00000000-0000-0000-0000-000000000000'));
        $pointsEarningRule2->setPointValue(4);
        $pointsEarningRule2->setExcludeDeliveryCost(false);

        $evaluator = $this->getEarningRuleEvaluator([$purchaseEarningRule, $pointsEarningRule2]);

        $points = $evaluator->evaluateTransaction(new TransactionId('00000000-0000-0000-0000-000000000000'), new CustomerId(static::USER_ID));
        $this->assertEquals(708, $points);
    }

    /**
     * @test
     */
    public function it_returns_proper_value_for_event()
    {
        $eventEarningRule = new EventEarningRule(new EarningRuleId('00000000-0000-0000-0000-000000000000'));
        $eventEarningRule->setEventName(AccountSystemEvents::ACCOUNT_CREATED);
        $eventEarningRule->setPointsAmount(200);

        $evaluator = $this->getEarningRuleEvaluator([$eventEarningRule]);
        $customerId = 11;
        $points = $evaluator->evaluateEvent(AccountSystemEvents::ACCOUNT_CREATED, $customerId);
        $this->assertEquals(200, $points);
    }

    /**
     * @test
     */
    public function it_returns_proper_value_for_given_transaction_and_points_earning_rule_if_excluded_label()
    {
        $pointsEarningRule = new PointsEarningRule(new EarningRuleId('00000000-0000-0000-0000-000000000000'));
        $pointsEarningRule->setPointValue(4);
        $pointsEarningRule->setExcludeDeliveryCost(false);
        $pointsEarningRule->setExcludedLabels([new Label('color', 'red')]);

        $evaluator = $this->getEarningRuleEvaluator([$pointsEarningRule]);

        $points = $evaluator->evaluateTransaction(new TransactionId('00000000-0000-0000-0000-000000000000'), new CustomerId(static::USER_ID));
        $this->assertEquals(560, $points);
    }

    /**
     * @test
     */
    public function it_returns_proper_value_for_given_transaction_and_points_earning_rule_if_excluded_sku()
    {
        $pointsEarningRule = new PointsEarningRule(new EarningRuleId('00000000-0000-0000-0000-000000000000'));
        $pointsEarningRule->setPointValue(4);
        $pointsEarningRule->setExcludeDeliveryCost(false);
        $pointsEarningRule->setExcludedSKUs([new SKU('000')]);

        $evaluator = $this->getEarningRuleEvaluator([$pointsEarningRule]);

        $points = $evaluator->evaluateTransaction(new TransactionId('00000000-0000-0000-0000-000000000000'), new CustomerId(static::USER_ID));
        $this->assertEquals(208, $points);
    }

    /**
     * @test
     */
    public function it_returns_proper_value_for_given_transaction_and_multiply_points_rule_by_sku()
    {
        $pointsEarningRule = new PointsEarningRule(new EarningRuleId('00000000-0000-0000-0000-000000000000'));
        $pointsEarningRule->setPointValue(4);
        $pointsEarningRule->setExcludeDeliveryCost(false);

        $multiplyPointsEarningRule = new MultiplyPointsForProductEarningRule(
            new EarningRuleId('00000000-0000-0000-0000-000000000000')
        );
        $multiplyPointsEarningRule->setMultiplier(3);
        $multiplyPointsEarningRule->setSkuIds([new SKU('123')]);

        $evaluator = $this->getEarningRuleEvaluator([$pointsEarningRule, $multiplyPointsEarningRule]);

        $points = $evaluator->evaluateTransaction(new TransactionId('00000000-0000-0000-0000-000000000000'), new CustomerId(static::USER_ID));
        $this->assertEquals(704, $points);
    }

    /**
     * @test
     */
    public function it_returns_proper_value_for_given_transaction_and_multiply_points_rule_by_label()
    {
        $pointsEarningRule = new PointsEarningRule(new EarningRuleId('00000000-0000-0000-0000-000000000000'));
        $pointsEarningRule->setPointValue(4);
        $pointsEarningRule->setExcludeDeliveryCost(false);

        $multiplyPointsEarningRule = new MultiplyPointsForProductEarningRule(
            new EarningRuleId('00000000-0000-0000-0000-000000000000')
        );
        $multiplyPointsEarningRule->setMultiplier(3);
        $multiplyPointsEarningRule->setLabels([new Label('color', 'red')]);

        $evaluator = $this->getEarningRuleEvaluator([$pointsEarningRule, $multiplyPointsEarningRule]);

        $points = $evaluator->evaluateTransaction(new TransactionId('00000000-0000-0000-0000-000000000000'), new CustomerId(static::USER_ID));
        $this->assertEquals(704, $points);
    }

    /**
     * @test
     */
    public function it_returns_proper_value_for_given_transaction_and_points_earning_with_above_minimal()
    {
        $pointsEarningRule = new PointsEarningRule(new EarningRuleId('00000000-0000-0000-0000-000000000000'));
        $pointsEarningRule->setPointValue(4);
        $pointsEarningRule->setExcludeDeliveryCost(false);
        $pointsEarningRule->setMinOrderValue(100);

        $evaluator = $this->getEarningRuleEvaluator([$pointsEarningRule]);

        $points = $evaluator->evaluateTransaction(new TransactionId('00000000-0000-0000-0000-000000000000'), new CustomerId(static::USER_ID));
        $this->assertEquals(608, $points);
    }

    /**
     * @test
     */
    public function it_returns_proper_value_for_given_transaction_and_points_earning_with_bellow_minimal()
    {
        $pointsEarningRule = new PointsEarningRule(new EarningRuleId('00000000-0000-0000-0000-000000000000'));
        $pointsEarningRule->setPointValue(4);
        $pointsEarningRule->setExcludeDeliveryCost(false);
        $pointsEarningRule->setMinOrderValue(300);

        $evaluator = $this->getEarningRuleEvaluator([$pointsEarningRule]);

        $points = $evaluator->evaluateTransaction(new TransactionId('00000000-0000-0000-0000-000000000000'), new CustomerId(static::USER_ID));
        $this->assertEquals(0, $points);
    }

    /**
     * @test
     */
    public function it_returns_proper_value_for_given_transaction_and_order_rules()
    {
        $pointsEarningRule = new PointsEarningRule(new EarningRuleId('00000000-0000-0000-0000-000000000000'));
        $pointsEarningRule->setPointValue(4);
        $pointsEarningRule->setExcludeDeliveryCost(false);

        $pointsEarningRule2 = new PointsEarningRule(new EarningRuleId('00000000-0000-0000-0000-000000000000'));
        $pointsEarningRule2->setPointValue(10);
        $pointsEarningRule2->setExcludeDeliveryCost(false);

        $multiplyPointsEarningRule = new MultiplyPointsForProductEarningRule(
            new EarningRuleId('00000000-0000-0000-0000-000000000000')
        );
        $multiplyPointsEarningRule->setMultiplier(3);
        $multiplyPointsEarningRule->setLabels([new Label('color', 'red')]);

        $multiplyPointsEarningRule2 = new MultiplyPointsForProductEarningRule(
            new EarningRuleId('00000000-0000-0000-0000-000000000000')
        );
        $multiplyPointsEarningRule2->setMultiplier(5);
        $multiplyPointsEarningRule2->setLabels([new Label('color', 'blue')]);

        $purchaseEarningRule = new ProductPurchaseEarningRule(
            new EarningRuleId('00000000-0000-0000-0000-000000000000')
        );
        $purchaseEarningRule->setSkuIds(['000']);
        $purchaseEarningRule->setPointsAmount(200);

        $evaluator = $this->getEarningRuleEvaluator(
            [$pointsEarningRule, $pointsEarningRule2, $multiplyPointsEarningRule, $multiplyPointsEarningRule2, $purchaseEarningRule]
        );

        $points = $evaluator->evaluateTransaction(new TransactionId('00000000-0000-0000-0000-000000000000'), new CustomerId(static::USER_ID));
        $this->assertEquals(8264, $points);
    }

    /**
     * @param array $rules
     * @return OloyEarningRuleEvaluator
     */
    protected function getEarningRuleEvaluator(array $rules)
    {
        return new OloyEarningRuleEvaluator(
            $this->getEarningRuleRepository($rules),
            $this->getTransactionDetailsRepository(),
            $this->getEarningRuleAlgorithmFactory(),
            $this->getInvitationDetailsRepository(),
            $this->getSegmentedCustomersRepository(),
            $this->getCustomerDetailsRepository()
        );
    }

    /**
     * @return EarningRuleAlgorithmFactoryInterface
     */
    protected function getEarningRuleAlgorithmFactory()
    {
        $algorithms = [
            PointsEarningRule::class => new PointsEarningRuleAlgorithm(0),
            MultiplyPointsForProductEarningRule::class => new MultiplyPointsForProductRuleAlgorithm(2),
            ProductPurchaseEarningRule::class => new ProductPurchaseEarningRuleAlgorithm(3),
        ];

        $mock = $this->createMock(EarningRuleAlgorithmFactoryInterface::class);
        $mock->method('getAlgorithm')->will(
            $this->returnCallback(
                function ($class) use ($algorithms) {
                    return $algorithms[get_class($class)];
                }
            )
        );

        return $mock;
    }

    /**
     * @return TransactionDetailsRepository
     */
    protected function getTransactionDetailsRepository()
    {
        $transactionDetails = new TransactionDetails(
            new \OpenLoyalty\Domain\Transaction\TransactionId('00000000-0000-0000-0000-000000000000')
        );
        $transactionDetails->setItems(
            [
                new Item(
                    new SKU('123'), 'item1', 1, 12, 'cat', $maker = 'test', [
                        new Label('color', 'red'),
                    ]
                ),
                new Item(
                    new SKU('000'), 'item2', 1, 100, 'cat', $maker = 'test', [
                        new Label('color', 'blue'),
                    ]
                ),
                new Item(
                    new SKU('0001'), 'delivery', 1, 40, 'cat', $maker = 'test'
                ),
            ]
        );
        $transactionDetails->setExcludedDeliverySKUs(['0001']);

        $mock = $this->createMock(TransactionDetailsRepository::class);
        $mock->method('find')->with($this->isType('string'))
            ->willReturn($transactionDetails);

        return $mock;
    }

    /**
     * @return InvitationDetailsRepository
     */
    protected function getInvitationDetailsRepository()
    {
        $mock = $this->createMock(InvitationDetailsRepository::class);
        $mock->method('find')->with($this->isType('string'))
            ->willReturn([]);

        return $mock;
    }

    /**
     * @param array $earningRules
     * @return EarningRuleRepository
     */
    protected function getEarningRuleRepository(array $earningRules)
    {
        $mock = $this->createMock(EarningRuleRepository::class);
        $mock->method('findAllActive')
            ->with(
                $this->logicalOr(
                    $this->isInstanceOf(\DateTime::class),
                    $this->isNull()
                )
            )
            ->willReturn(
                $earningRules
            );
        $mock->method('findAllActiveEventRules')->with(
            $this->isType('string'),
            $this->logicalOr(
                $this->isInstanceOf(\DateTime::class),
                $this->isNull()
            )
        )
            ->willReturn($earningRules);

        return $mock;
    }

    protected function getSegmentedCustomersRepository()
    {
        $mock = $this->createMock(SegmentedCustomersRepository::class);

        $dataToReturn = [];

        $mock->method('findByParameters')
            ->with(
                $this->isType('array'),
                $this->isType('bool')
            )->willReturn($dataToReturn);

        return $mock;
    }

    protected function getCustomerDetailsRepository()
    {
        $mock = $this->createMock(CustomerDetailsRepository::class);

        $dataToReturn = [];

        $mock->method('findOneByCriteria')
            ->with(
                $this->isType('array'),
                $this->isType('int')
            )->willReturn($dataToReturn);

        return $mock;

    }
}
