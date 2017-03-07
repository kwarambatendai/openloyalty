<?php

namespace OpenLoyalty\Bundle\AnalyticsBundle\Security\Voter;

use OpenLoyalty\Bundle\BaseVoterTest;

/**
 * Class AnalyticsVoterTest.
 */
class AnalyticsVoterTest extends BaseVoterTest
{
    /**
     * @test
     */
    public function it_works()
    {
        $attributes = [
            AnalyticsVoter::VIEW_STATS => ['seller' => false, 'customer' => false, 'admin' => true],
        ];

        $voter = new AnalyticsVoter();

        $this->makeAssertions($attributes, $voter);
    }

    protected function getSubjectById($id)
    {
        return null;
    }
}
