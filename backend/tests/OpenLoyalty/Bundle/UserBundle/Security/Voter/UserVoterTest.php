<?php

namespace OpenLoyalty\Bundle\UserBundle\Security\Voter;

use OpenLoyalty\Bundle\BaseVoterTest;

/**
 * Class UserVoterTest.
 */
class UserVoterTest extends BaseVoterTest
{
    /**
     * @test
     */
    public function it_works()
    {
        $attributes = [
            UserVoter::PASSWORD_CHANGE => ['seller' => true, 'customer' => true, 'admin' => true],
            UserVoter::REVOKE_REFRESH_TOKEN => ['seller' => true, 'customer' => true, 'admin' => true],
        ];

        $voter = new UserVoter();

        $this->makeAssertions($attributes, $voter);
    }

    protected function getSubjectById($id)
    {
        return null;
    }
}
