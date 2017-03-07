<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\UserBundle\Service;

use OpenLoyalty\Bundle\SettingsBundle\Service\SettingsManager;
use OpenLoyalty\Infrastructure\Customer\TierAssignTypeProvider;

/**
 * Class SettingsBasedTierAssignTypeProvider.
 */
class SettingsBasedTierAssignTypeProvider implements TierAssignTypeProvider
{
    /**
     * @var SettingsManager
     */
    protected $settingsManager;

    /**
     * SettingsBasedTierAssignTypeProvider constructor.
     *
     * @param SettingsManager $settingsManager
     */
    public function __construct(SettingsManager $settingsManager)
    {
        $this->settingsManager = $settingsManager;
    }

    /**
     * @return string
     */
    public function getType()
    {
        $type = $this->settingsManager->getSettingByKey('tierAssignType');
        if (!$type) {
            return;
        }

        $value = $type->getValue();

        if ($value == TierAssignTypeProvider::TYPE_TRANSACTIONS || $value == TierAssignTypeProvider::TYPE_POINTS) {
            return $value;
        }

        return;
    }
}
