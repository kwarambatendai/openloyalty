<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\SettingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class BooleanSettingEntry.
 *
 * @ORM\Entity()
 */
class BooleanSettingEntry extends SettingsEntry
{
    /**
     * @var bool
     * @ORM\Column(type="boolean", name="boolean_value")
     */
    protected $booleanValue;

    public function setValue($value)
    {
        $this->booleanValue = (bool) $value;
    }

    public function getValue()
    {
        return $this->booleanValue;
    }
}
