<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\SettingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class IntegerSettingEntry.
 *
 * @ORM\Entity()
 */
class IntegerSettingEntry extends SettingsEntry
{
    /**
     * @var int
     * @ORM\Column(type="integer", name="integer_value", nullable=true)
     */
    protected $integerValue;

    public function setValue($value)
    {
        $this->integerValue = $value;
    }

    public function getValue()
    {
        return $this->integerValue;
    }
}
