<?php

namespace OpenLoyalty\Bundle\UserBundle\Security\Voter;

use OpenLoyalty\Bundle\BaseVoterTest;
use OpenLoyalty\Bundle\UserBundle\DataFixtures\ORM\LoadUserData;
use OpenLoyalty\Bundle\UserBundle\Entity\Admin;

/**
 * Class AdminVoterTest.
 */
class AdminVoterTest extends BaseVoterTest
{
    /**
     * @test
     */
    public function it_works()
    {
        $attributes = [
            AdminVoter::VIEW => ['seller' => false, 'customer' => false, 'admin' => true, 'id' => ''],
            AdminVoter::EDIT => ['seller' => false, 'customer' => false, 'admin' => true, 'id' => ''],
        ];

        $voter = new AdminVoter();

        $this->makeAssertions($attributes, $voter);
    }

    protected function getSubjectById($id)
    {
        $admin = $this->getMockBuilder(Admin::class)->disableOriginalConstructor()->getMock();
        $admin->method('getId')->willReturn(LoadUserData::ADMIN_ID);

        return $admin;
    }
}
