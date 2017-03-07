<?php

namespace OpenLoyalty\Bundle\LevelBundle\Model;

use OpenLoyalty\Domain\Level\Model\Reward as BaseReward;

/**
 * Class Reward.
 */
class Reward extends BaseReward
{
    public function __construct()
    {
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param float $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'name' => $this->getName(),
            'value' => $this->getValue(),
            'code' => $this->getCode(),
        ];
    }
}
