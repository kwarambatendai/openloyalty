<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UserBundle\Service;

use OpenLoyalty\Bundle\SettingsBundle\Service\SettingsManager;
use OpenLoyalty\Infrastructure\Customer\ExcludeDeliveryCostsProvider;

/**
 * Class SettingsBasedExcludeDeliveryCostsProvider.
 */
class SettingsBasedExcludeDeliveryCostsProvider implements ExcludeDeliveryCostsProvider
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
     * @return bool
     */
    public function areExcluded()
    {
        $ex = $this->settingsManager->getSettingByKey('excludeDeliveryCostsFromTierAssignment');
        if (!$ex) {
            return false;
        }

        return true;
    }
}
