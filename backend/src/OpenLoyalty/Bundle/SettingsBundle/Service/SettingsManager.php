<?php

namespace OpenLoyalty\Bundle\SettingsBundle\Service;

use OpenLoyalty\Bundle\SettingsBundle\Entity\SettingsEntry;
use OpenLoyalty\Bundle\SettingsBundle\Model\Settings;

interface SettingsManager
{
    public function save(Settings $settings, $flush = true);

    /**
     * @return Settings
     */
    public function getSettings();

    /**
     * @param $key
     *
     * @return SettingsEntry
     */
    public function getSettingByKey($key);
}
