<?php

namespace OpenLoyalty\Bundle\EarningRuleBundle\Security\Voter;

use OpenLoyalty\Bundle\BaseVoterTest;
use OpenLoyalty\Domain\EarningRule\EarningRule;
use OpenLoyalty\Domain\EarningRule\EarningRuleId;

/**
 * Class EarningRuleVoterTest.
 */
class EarningRuleVoterTest extends BaseVoterTest
{
    const EARNING_RULE_ID = '00000000-0000-474c-b092-b0dd880c0700';
    const EARNING_RULE2_ID = '00000000-0000-474c-b092-b0dd880c0702';

    /**
     * @test
     */
    public function it_works()
    {
        $attributes = [
            EarningRuleVoter::CREATE_EARNING_RULE => ['seller' => false, 'customer' => false, 'admin' => true],
            EarningRuleVoter::EDIT => ['seller' => false, 'customer' => false, 'admin' => true, 'id' => self::EARNING_RULE_ID],
            EarningRuleVoter::VIEW => ['seller' => true, 'customer' => false, 'admin' => true, 'id' => self::EARNING_RULE2_ID],
            EarningRuleVoter::LIST_ALL_EARNING_RULES => ['seller' => true, 'customer' => false, 'admin' => true],
            EarningRuleVoter::LIST_ACTIVE_EARNING_RULES => ['seller' => false, 'customer' => true, 'admin' => true],
        ];

        $voter = new EarningRuleVoter();

        $this->makeAssertions($attributes, $voter);
    }

    protected function getSubjectById($id)
    {
        $earningRule = $this->getMockBuilder(EarningRule::class)->disableOriginalConstructor()->getMock();
        $earningRule->method('getEarningRuleId')->willReturn(new EarningRuleId($id));

        return $earningRule;
    }
}
