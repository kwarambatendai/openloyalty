<?php

namespace OpenLoyalty\Bundle\SettingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class JsonSettingEntry.
 *
 * @ORM\Entity()
 */
class JsonSettingEntry extends SettingsEntry implements \ArrayAccess, \IteratorAggregate
{
    /**
     * @var array
     * @ORM\Column(type="json_array", name="json_value")
     */
    protected $jsonValue;

    public function setValue($value)
    {
        $this->jsonValue = $value;
    }

    public function getValue()
    {
        $array = $this->jsonValue;
        usort($array, function ($a, $b) {
            if (isset($a['priority']) && isset($b['priority'])) {
                if ($a['priority'] == $b['priority']) {
                    return 0;
                }

                return $a['priority'] < $b['priority'] ? -1 : 1;
            }

            return 0;
        });

        return $array;
    }

    public function offsetExists($offset)
    {
        return isset($this->jsonValue[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->jsonValue[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->jsonValue[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->jsonValue[$offset]);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->jsonValue);
    }
}
