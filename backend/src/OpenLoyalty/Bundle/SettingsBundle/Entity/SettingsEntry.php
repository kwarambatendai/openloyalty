<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\SettingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class SettingsEntry.
 *
 * @ORM\Entity()
 * @ORM\Table(name="ol__settings")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @UniqueEntity(fields={"key"})
 */
abstract class SettingsEntry
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", name="setting_key")
     */
    protected $key;

    /**
     * SettingsEntry constructor.
     *
     * @param string $key
     * @param null   $value
     */
    public function __construct($key, $value = null)
    {
        $this->key = $key;
        if ($value) {
            $this->setValue($value);
        }
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    abstract public function setValue($value);

    abstract public function getValue();
}
