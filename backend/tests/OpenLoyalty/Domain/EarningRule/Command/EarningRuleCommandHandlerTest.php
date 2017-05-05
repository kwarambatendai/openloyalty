<?php

namespace OpenLoyalty\Domain\EarningRule\Command;

use Broadway\UuidGenerator\Rfc4122\Version4Generator;
use OpenLoyalty\Domain\EarningRule\EarningRule;
use OpenLoyalty\Domain\EarningRule\EarningRuleId;
use OpenLoyalty\Domain\EarningRule\EarningRuleRepository;

/**
 * Class EarningRuleCommandHandlerTest.
 */
abstract class EarningRuleCommandHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected $inMemoryRepository;

    protected $rules = [];

    public function setUp()
    {
        $rules = &$this->rules;
        $this->inMemoryRepository = $this->getMockBuilder(EarningRuleRepository::class)->getMock();
        $this->inMemoryRepository->method('save')->with($this->isInstanceOf(EarningRule::class))->will(
            $this->returnCallback(function($rule) use (&$rules) {
                $rules[] = $rule;

                return $rule;
            })
        );
        $this->inMemoryRepository->method('findAll')->with()->will(
            $this->returnCallback(function() use (&$rules) {
                return $rules;
            })
        );
        $this->inMemoryRepository->method('byId')->with($this->isInstanceOf(EarningRuleId::class))->will(
            $this->returnCallback(function($id) use (&$rules) {
                /** @var EarningRule $rule */
                foreach ($rules as $rule) {
                    if ($rule->getEarningRuleId()->__toString() == $id->__toString()) {
                        return $rule;
                    }
                }

                return null;
            })
        );
    }

    protected function createCommandHandler()
    {
        $uuidGenerator = new Version4Generator();

        return new EarningRuleCommandHandler(
            $this->inMemoryRepository,
            $uuidGenerator
        );
    }
}
