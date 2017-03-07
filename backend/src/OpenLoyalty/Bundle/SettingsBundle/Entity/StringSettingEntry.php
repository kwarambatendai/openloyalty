<?php

namespace OpenLoyalty\Bundle\SettingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class StringSettingEntry.
 *
 * @ORM\Entity()
 */
class StringSettingEntry extends SettingsEntry
{
    /**
     * @var string
     * @ORM\Column(type="string", name="string_value")
     */
    protected $stringValue;

    public function setValue($value)
    {
        $this->stringValue = (string) $value;
    }

    public function getValue()
    {
        return $this->stringValue;
    }
}
