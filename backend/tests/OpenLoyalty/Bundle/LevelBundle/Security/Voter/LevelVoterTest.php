<?php

namespace OpenLoyalty\Bundle\LevelBundle\Security\Voter;

use OpenLoyalty\Bundle\BaseVoterTest;
use OpenLoyalty\Domain\Level\Level;
use OpenLoyalty\Domain\Level\LevelId;

/**
 * Class LevelVoterTest.
 */
class LevelVoterTest extends BaseVoterTest
{
    const LEVEL_ID = '00000000-0000-474c-b092-b0dd880c0700';
    const LEVEL2_ID = '00000000-0000-474c-b092-b0dd880c0702';

    /**
     * @test
     */
    public function it_works()
    {
        $attributes = [
            LevelVoter::CREATE_LEVEL => ['seller' => false, 'customer' => false, 'admin' => true],
            LevelVoter::EDIT => ['seller' => false, 'customer' => false, 'admin' => true, 'id' => self::LEVEL_ID],
            LevelVoter::VIEW => ['seller' => true, 'customer' => false, 'admin' => true, 'id' => self::LEVEL2_ID],
            LevelVoter::ACTIVATE => ['seller' => false, 'customer' => false, 'admin' => true, 'id' => self::LEVEL2_ID],
            LevelVoter::LIST_LEVELS=> ['seller' => true, 'customer' => false, 'admin' => true],
            LevelVoter::LIST_CUSTOMERS => ['seller' => false, 'customer' => false, 'admin' => true, 'id' => self::LEVEL2_ID],
        ];

        $voter = new LevelVoter();

        $this->makeAssertions($attributes, $voter);
    }

    protected function getSubjectById($id)
    {
        $level = $this->getMockBuilder(Level::class)->disableOriginalConstructor()->getMock();
        $level->method('getLevelId')->willReturn(new LevelId($id));

        return $level;
    }
}