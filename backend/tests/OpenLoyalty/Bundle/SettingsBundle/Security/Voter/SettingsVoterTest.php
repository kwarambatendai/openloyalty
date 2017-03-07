<?php

namespace OpenLoyalty\Bundle\SettingsBundle\Security\Voter;

use OpenLoyalty\Bundle\BaseVoterTest;

/**
 * Class SettingsVoterTest.
 */
class SettingsVoterTest extends BaseVoterTest
{
    /**
     * @test
     */
    public function it_works()
    {
        $attributes = [
            SettingsVoter::VIEW_SETTINGS => ['seller' => false, 'customer' => false, 'admin' => true],
            SettingsVoter::VIEW_SETTINGS_CHOICES => ['seller' => true, 'customer' => true, 'admin' => true],
            SettingsVoter::EDIT_SETTINGS => ['seller' => false, 'customer' => false, 'admin' => true],
        ];

        $voter = new SettingsVoter();

        $this->makeAssertions($attributes, $voter);
    }

    protected function getSubjectById($id)
    {
        return;
    }
}
