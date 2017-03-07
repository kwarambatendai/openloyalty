<?php

namespace OpenLoyalty\Bundle\SettingsBundle\Model;

use OpenLoyalty\Bundle\SettingsBundle\Entity\SettingsEntry;

/**
 * Class Settings.
 */
class Settings
{
    /**
     * @var SettingsEntry[]
     */
    protected $entries = [];

    public function __get($name)
    {
        return $this->getEntry($name);
    }

    public function __set($name, $value)
    {
        $this->addEntry($value);
    }

    public function addEntry(SettingsEntry $entry)
    {
        $this->entries[$entry->getKey()] = $entry;
    }

    public function getEntry($key)
    {
        if (!isset($this->entries[$key])) {
            return;
        }

        return $this->entries[$key];
    }

    /**
     * @return \OpenLoyalty\Bundle\SettingsBundle\Entity\SettingsEntry[]
     */
    public function getEntries()
    {
        return $this->entries;
    }

    public static function fromArray(array $entries)
    {
        $settings = new self();
        foreach ($entries as $entry) {
            if ($entry instanceof SettingsEntry) {
                $settings->addEntry($entry);
            }
        }

        return $settings;
    }

    public function toArray()
    {
        $ret = [];
        foreach ($this->entries as $entry) {
            $ret[$entry->getKey()] = $entry->getValue();
        }

        return $ret;
    }
}
