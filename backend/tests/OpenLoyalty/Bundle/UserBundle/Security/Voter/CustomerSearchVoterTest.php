<?php

namespace OpenLoyalty\Bundle\UserBundle\Security\Voter;

use OpenLoyalty\Bundle\BaseVoterTest;
use OpenLoyalty\Bundle\UserBundle\Security\Voter\CustomerSearchVoter;

/**
 * Class CustomerSearchVoterTest.
 */
class CustomerSearchVoterTest extends BaseVoterTest
{
    /**
     * @test
     */
    public function it_works()
    {
        $attributes = [
            CustomerSearchVoter::SEARCH_CUSTOMER => ['seller' => true, 'customer' => false, 'admin' => true],
        ];

        $voter = new CustomerSearchVoter();

        $this->makeAssertions($attributes, $voter);
    }

    protected function getSubjectById($id)
    {
        return null;
    }
}
