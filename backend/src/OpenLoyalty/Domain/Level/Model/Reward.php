<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Level\Model;

use Assert\Assertion as Assert;

/**
 * Class Reward.
 */
class Reward
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var float
     */
    protected $value;

    /**
     * @var string
     */
    protected $code;

    /**
     * Reward constructor.
     *
     * @param string $name
     * @param float  $value
     * @param string $code
     */
    public function __construct($name, $value, $code)
    {
        Assert::notEmpty($name);
        Assert::notEmpty($value);
        Assert::notEmpty($code);

        $this->name = $name;
        $this->value = $value;
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}
