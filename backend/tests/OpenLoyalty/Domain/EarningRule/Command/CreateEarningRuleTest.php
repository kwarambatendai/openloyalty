<?php

namespace OpenLoyalty\Domain\EarningRule\Command;

use OpenLoyalty\Domain\EarningRule\EarningRule;
use OpenLoyalty\Domain\EarningRule\EarningRuleId;
use OpenLoyalty\Domain\EarningRule\EventEarningRule;
use OpenLoyalty\Domain\EarningRule\PointsEarningRule;
use OpenLoyalty\Domain\EarningRule\ProductPurchaseEarningRule;

/**
 * Class CreateEarningRuleTest.
 */
class CreateEarningRuleTest extends EarningRuleCommandHandlerTest
{
    /**
     * @test
     */
    public function it_creates_new_event_earning_rule()
    {
        $handler = $this->createCommandHandler();
        $ruleId = new EarningRuleId('00000000-0000-0000-0000-000000000000');

        $command = new CreateEarningRule($ruleId, EarningRule::TYPE_EVENT, [
            'name' => 'test',
            'description' => 'desc',
            'startAt' => (new \DateTime())->getTimestamp(),
            'endAt' => (new \DateTime('+1 month'))->getTimestamp(),
            'eventName' => 'event',
            'pointsAmount' => 100,
        ]);
        $handler->handle($command);
        $rule = $this->inMemoryRepository->byId($ruleId);
        $this->assertNotNull($rule);
        $this->assertInstanceOf(EventEarningRule::class, $rule);
    }

    /**
     * @test
     * @expectedException \Assert\InvalidArgumentException
     */
    public function it_throws_exception_on_empty_name()
    {
        $handler = $this->createCommandHandler();
        $ruleId = new EarningRuleId('00000000-0000-0000-0000-000000000000');

        $command = new CreateEarningRule($ruleId, EarningRule::TYPE_EVENT, [
            'description' => 'desc',
            'startAt' => (new \DateTime())->getTimestamp(),
            'endAt' => (new \DateTime('+1 month'))->getTimestamp(),
            'eventName' => 'event',
            'pointsAmount' => 100,
        ]);
        $handler->handle($command);
    }

    /**
     * @test
     */
    public function it_creates_new_points_earning_rule()
    {
        $handler = $this->createCommandHandler();
        $ruleId = new EarningRuleId('00000000-0000-0000-0000-000000000000');

        $command = new CreateEarningRule($ruleId, EarningRule::TYPE_POINTS, [
            'name' => 'test',
            'description' => 'desc',
            'startAt' => (new \DateTime())->getTimestamp(),
            'endAt' => (new \DateTime('+1 month'))->getTimestamp(),
            'pointValue' => 3.3,
        ]);
        $handler->handle($command);
        $rule = $this->inMemoryRepository->byId($ruleId);
        $this->assertNotNull($rule);
        $this->assertInstanceOf(PointsEarningRule::class, $rule);
    }

    /**
     * @test
     */
    public function it_creates_new_product_purchase_earning_rule()
    {
        $handler = $this->createCommandHandler();
        $ruleId = new EarningRuleId('00000000-0000-0000-0000-000000000000');

        $command = new CreateEarningRule($ruleId, EarningRule::TYPE_PRODUCT_PURCHASE, [
            'name' => 'test',
            'description' => 'desc',
            'startAt' => (new \DateTime())->getTimestamp(),
            'endAt' => (new \DateTime('+1 month'))->getTimestamp(),
            'skuIds' => ['123'],
            'pointsAmount' => 100,
        ]);
        $handler->handle($command);
        $rule = $this->inMemoryRepository->byId($ruleId);
        $this->assertNotNull($rule);
        $this->assertInstanceOf(ProductPurchaseEarningRule::class, $rule);
    }
}
